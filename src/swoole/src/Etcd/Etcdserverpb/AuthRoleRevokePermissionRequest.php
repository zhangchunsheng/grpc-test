<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: rpc.proto

namespace Etcdserverpb;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>etcdserverpb.AuthRoleRevokePermissionRequest</code>
 */
class AuthRoleRevokePermissionRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string role = 1;</code>
     */
    private $role = '';
    /**
     * Generated from protobuf field <code>bytes key = 2;</code>
     */
    private $key = '';
    /**
     * Generated from protobuf field <code>bytes range_end = 3;</code>
     */
    private $range_end = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $role
     *     @type string $key
     *     @type string $range_end
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Rpc::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string role = 1;</code>
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Generated from protobuf field <code>string role = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setRole($var)
    {
        GPBUtil::checkString($var, True);
        $this->role = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes key = 2;</code>
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Generated from protobuf field <code>bytes key = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setKey($var)
    {
        GPBUtil::checkString($var, False);
        $this->key = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes range_end = 3;</code>
     * @return string
     */
    public function getRangeEnd()
    {
        return $this->range_end;
    }

    /**
     * Generated from protobuf field <code>bytes range_end = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setRangeEnd($var)
    {
        GPBUtil::checkString($var, False);
        $this->range_end = $var;

        return $this;
    }

}

