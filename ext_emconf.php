<?php

########################################################################
# Extension Manager/Repository config file for ext "fal_recycler".
########################################################################

$EM_CONF[$_EXTKEY] = array(
    'title' => 'FAL Recycler for 6.2',
    'description' => 'Enables FAL Recycler Feature in TYPO3 6.2',
    'category' => 'misc',
    'author' => 'Dominique Kreemers, Marcus Schwemer',
    'author_email' => 'marcus.schwemer@in2code.de',
    'dependencies' => 'extbase, fluid',
    'state' => 'stable',
    'author_company' => 'in2code GmbH',
    'version' => '1.0.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '6.2.0-6.2.99',
            'extbase' => '6.2.0-6.2.99',
            'fluid' => '6.2.0-6.2.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
