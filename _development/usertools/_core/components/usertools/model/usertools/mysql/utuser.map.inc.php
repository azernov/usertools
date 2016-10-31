<?php
$xpdo_meta_map['utUser']= array (
  'package' => 'usertools',
  'version' => '1.1',
  'extends' => 'modUser',
  'fields' => 
  array (
    'class_key' => 'utUser',
  ),
  'fieldMeta' => 
  array (
    'class_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => 'utUser',
    ),
  ),
  'composites' => 
  array (
    'Data' => 
    array (
      'class' => 'utUserData',
      'local' => 'id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
  'validation' => 
  array (
    'rules' => 
    array (
      'email' => 
      array (
        'email' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkEmail',
        ),
      ),
      'firstname' => 
      array (
        'firstname' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkName',
        ),
      ),
      'middlename' => 
      array (
        'middlename' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkName',
        ),
      ),
      'lastname' => 
      array (
        'lastname' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkName',
        ),
      ),
      'phone' => 
      array (
        'phone' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkPhone',
        ),
      ),
      'old_password' => 
      array (
        'old_password' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkOldPassword',
        ),
      ),
      'password' => 
      array (
        'password' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkPassword',
        ),
      ),
      'password2' => 
      array (
        'password2' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkPassword2',
        ),
      ),
      'type' => 
      array (
        'type' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkType',
        ),
      ),
      'company' => 
      array (
        'company' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkCompany',
        ),
      ),
      'website' => 
      array (
        'website' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkWebsite',
        ),
      ),
      'inn' => 
      array (
        'inn' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkInn',
        ),
      ),
      'accept_rules' => 
      array (
        'accept_rules' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'validation.utuser.checkAcceptRules',
        ),
      ),
    ),
  ),
);
