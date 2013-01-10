<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{

	 // Need to store the user's ID:
	 private $_id;


	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$user = User::model()->findByAttributes(array('email'=>$this->username));

		if ($user===null) { // No user found!
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		//} else if ($user->password !== SHA1($this->password) ) { // Invalid password!
                } else if ($user->password !== $this->password ) { // Invalid password! //plain text
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		} else { // Okay!
		    $this->errorCode=self::ERROR_NONE;
		    // Store the role in a session:
		    $this->setState('role', $user->role);
			$this->_id = $user->user_id;
                        
                        //rbac
                        
                        $auth=Yii::app()->authManager;
                        if(!$auth->isAssigned($user->role,$this->_id))
                        {
                            if($auth->assign($user->role,$this->_id))
                            {
                                Yii::app()->authManager->save();
                            }
                        }
		}
		return !$this->errorCode;
	}
	
	public function getId()
	{
	 return $this->_id;
	}

	
}