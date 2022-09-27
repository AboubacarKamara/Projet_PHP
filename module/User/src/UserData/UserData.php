<?php

declare(strict_types=1);

namespace User\UserData;

# Class to store User Data
class UserData
{
	protected $user_id;
	protected $nom;
    protected $prenom;
	protected $email;
	protected $password;
	protected $birthday;
	protected $medicaments;

	public function __get($property) {
		if (property_exists($this, $property)) {
		  return $this->$property;
		}
	  }
	
	  public function __set($property, $value) {
		if (property_exists($this, $property)) {
		  $this->$property = $value;
		}
	
		return $this;
	  }
}