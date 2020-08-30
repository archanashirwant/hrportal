<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* MeetingParticipantInfo File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Graph
* @copyright © Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
namespace Microsoft\Graph\Model;
/**
* MeetingParticipantInfo class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright © Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class MeetingParticipantInfo extends Entity
{

    /**
    * Gets the identity
    * Identity information of the participant.
    *
    * @return IdentitySet The identity
    */
    public function getIdentity()
    {
        if (array_key_exists("identity", $this->_propDict)) {
            if (is_a($this->_propDict["identity"], "Microsoft\Graph\Model\IdentitySet")) {
                return $this->_propDict["identity"];
            } else {
                $this->_propDict["identity"] = new IdentitySet($this->_propDict["identity"]);
                return $this->_propDict["identity"];
            }
        }
        return null;
    }

    /**
    * Sets the identity
    * Identity information of the participant.
    *
    * @param IdentitySet $val The value to assign to the identity
    *
    * @return MeetingParticipantInfo The MeetingParticipantInfo
    */
    public function setIdentity($val)
    {
        $this->_propDict["identity"] = $val;
         return $this;
    }
    /**
    * Gets the upn
    * User principal name of the participant.
    *
    * @return string The upn
    */
    public function getUpn()
    {
        if (array_key_exists("upn", $this->_propDict)) {
            return $this->_propDict["upn"];
        } else {
            return null;
        }
    }

    /**
    * Sets the upn
    * User principal name of the participant.
    *
    * @param string $val The value of the upn
    *
    * @return MeetingParticipantInfo
    */
    public function setUpn($val)
    {
        $this->_propDict["upn"] = $val;
        return $this;
    }
}
