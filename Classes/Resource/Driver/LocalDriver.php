<?php
namespace In2code\FalRecycler\Resource\Driver;

use TYPO3\CMS\Core\Resource\FolderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class LocalDriver extends \TYPO3\CMS\Core\Resource\Driver\LocalDriver
{
    /**
     * Moves a file or folder to the given directory, renaming the source in the process if
     * a file or folder of the same name already exists in the target path.
     *
     * @param string $filePath
     * @param string $recycleDirectory
     * @return bool
     */
    protected function recycleFileOrFolder($filePath, $recycleDirectory)
    {
        $destinationFile = $recycleDirectory . '/' . PathUtility::basename($filePath);
        if (file_exists($destinationFile)) {
            $timeStamp = \DateTimeImmutable::createFromFormat('U.u', microtime(true))->format('YmdHisu');
            $destinationFile = $recycleDirectory . '/' . $timeStamp . '_' . PathUtility::basename($filePath);
        }
        $result = rename($filePath, $destinationFile);
        return $result;
    }

    /**
     * Removes a file from the filesystem. This does not check if the file is
     * still used or if it is a bad idea to delete it for some other reason
     * this has to be taken care of in the upper layers (e.g. the Storage)!
     *
     * @param string $fileIdentifier
     * @return boolean TRUE if deleting the file succeeded
     * @throws \RuntimeException
     */
    public function deleteFile($fileIdentifier)
    {
        $filePath = $this->getAbsolutePath($fileIdentifier);
        $recycleDirectory = $this->getRecycleDirectory($filePath);
        if (!empty($recycleDirectory)) {
            $result = $this->recycleFileOrFolder($filePath, $recycleDirectory);
        } else {
            $result = unlink($filePath);
        }
        if ($result === false) {
            throw new \RuntimeException('Deletion of file ' . $fileIdentifier . ' failed.', 1320855304);
        }
        return $result;
    }

    /**
     * Removes a folder from this storage.
     *
     * @param string $folderIdentifier
     * @param boolean $deleteRecursively
     * @return boolean
     * @throws \TYPO3\CMS\Core\Resource\Exception\FileOperationErrorException
     * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidPathException
     */
    public function deleteFolder($folderIdentifier, $deleteRecursively = false)
    {
        $folderPath = $this->getAbsolutePath($folderIdentifier);
        $recycleDirectory = $this->getRecycleDirectory($folderPath);
        if (!empty($recycleDirectory)) {
            $result = $this->recycleFileOrFolder($folderPath, $recycleDirectory);
        } else {
            $result = GeneralUtility::rmdir($folderPath, $deleteRecursively);
        }
        if ($result === false) {
            throw new \TYPO3\CMS\Core\Resource\Exception\FileOperationErrorException(
                'Deleting folder "' . $folderIdentifier . '" failed.',
                1330119451
            );
        }
        return $result;
    }

    /**
     * Get the path of the nearest recycler folder of a given $path.
     * Return an empty string if there is no recycler folder available.
     *
     * @param string $path
     * @return string
     */
    protected function getRecycleDirectory($path)
    {
        $recyclerSubdirectory = array_search(FolderInterface::ROLE_RECYCLER, $this->mappingFolderNameToRole, true);
        if ($recyclerSubdirectory === false) {
            return '';
        }
        $rootDirectory = rtrim($this->getAbsolutePath($this->getRootLevelFolder()), '/');
        $searchDirectory = PathUtility::dirname($path);
        // Check if file or folder to be deleted is inside a recycler directory
        if ($this->getRole($searchDirectory) === FolderInterface::ROLE_RECYCLER) {
            $searchDirectory = PathUtility::dirname($searchDirectory);
            // Check if file or folder to be deleted is inside the root recycler
            if ($searchDirectory == $rootDirectory) {
                return '';
            }
            $searchDirectory = PathUtility::dirname($searchDirectory);
        }
        // Search for the closest recycler directory
        while ($searchDirectory) {
            $recycleDirectory = $searchDirectory . '/' . $recyclerSubdirectory;
            if (is_dir($recycleDirectory)) {
                return $recycleDirectory;
            } elseif ($searchDirectory === $rootDirectory) {
                return '';
            } else {
                $searchDirectory = PathUtility::dirname($searchDirectory);
            }
        }
        return '';
    }
}
