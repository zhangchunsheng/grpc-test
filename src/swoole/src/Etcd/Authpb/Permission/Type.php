<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: auth.proto

namespace Authpb\Permission;

/**
 * Protobuf type <code>authpb.Permission.Type</code>
 */
class Type
{
    /**
     * Generated from protobuf enum <code>READ = 0;</code>
     */
    const READ = 0;
    /**
     * Generated from protobuf enum <code>WRITE = 1;</code>
     */
    const WRITE = 1;
    /**
     * Generated from protobuf enum <code>READWRITE = 2;</code>
     */
    const READWRITE = 2;
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Type::class, \Authpb\Permission_Type::class);

