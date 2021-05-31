<?php

class Validate {
    
    private $_passed = false,
            $_errors = array(),
            $_db = null;
    
    public function __construct() {
        $this->_db = DB::getInstance();
    }
    
    public function check($source, $items = array()) {
        foreach($items as $item => $rules) {
            foreach($rules as $rule => $rule_value) {
                
                $value = trim($source[$item]);
                $item = escape($item);
                
                if($rule === 'required' && $rule_value === true && empty($value)) {
                    $this->addError("{$item} is required");
                } else if(!empty($value)) {
                    switch($rule) {
                        case 'min':
                            if(strlen($value) < $rule_value) {
                                $this->addError("{$item} must be a minimum of {$rule_value} characters.");
                            } 
                        break;
                        case 'max':
                            if(strlen($value) > $rule_value) {
                                $this->addError("{$item} can only be a maximum of {$rule_value} characters.");
                            } 
                        break;
                        case 'matches':
                            if($value != $source[$rule_value]) {
                                $this->addError("{$rule_value} must match {$item}.");
                            }
                        break;
                        case 'unique':
                            $check = $this->_db->get($rule_value, array($item, '=', $value));
                            if($check->count()) {
                                $this->addError("{$item} already exists.");
                            }
                        break;
                        case 'unique_except_this':
                            $params = array();
                            $params[] = $value;
                            $sql = "SELECT * FROM $rule_value[0] WHERE $item=? AND ";
                            foreach($rule_value as $rule2 => $rule_value2)
                            {
                                $sql .= "$rule2 != ?";
                                $params[] = $rule_value2;
                            }                            
                            $check = $this->_db->query($sql, $params);
                            if($check->error() == true || $check->count() > 0) {
                                $this->addError("{$item} already exists.");
                            }
                        break;
                        case 'alphanumeric':                            
                            if(!preg_match('/^[a-zA-Z]+[a-zA-Z0-9. ]+$/', $value)) {
                                $this->addError("{$item} must be alphanumeric.");
                            } 
                        break;
                        case 'mobile':                            
                            if(!preg_match('/^[0-9]{10}+$/', $value)) {
                                $this->addError("{$item} must be a valid 10 digit number.");
                            } 
                        break;
                        case 'email':
                            if($value != "")
                            {                        
                                if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                    $this->addError("{$item} must be a valid email id.");
                                }
                            } 
                        break;
                    }
                }
                
            }
        }
        
        if(empty($this->_errors)) {
            $this->_passed = true;
        }
        //var_dump($this->_errors);
        return $this;
    }
    
    private function addError($error) {
        $this->_errors[] = $error;
    }
    
    public function errors() {
        return $this->_errors;
    }
       
    public function passed() {
        return $this->_passed;
    }
}