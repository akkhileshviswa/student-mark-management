<?php

namespace App\Core;

/**
 * This class contains all the constants.
 */
class Constants
{
    /** @var string table names */
    public const TABLE_NAME_ADMIN = 'admin';
    public const TABLE_NAME_TEACHERS = 'teachers';
    public const TABLE_NAME_SUBJECTS = 'subjects';
    public const TABLE_NAME_STUDENTS = 'students';

    /** @var string column names */
    public const COLUMN_ID = 'id';
    public const COLUMN_NAME = 'name';
    public const COLUMN_USERNAME = 'username';
    public const COLUMN_PASSWORD = 'password';
    public const COLUMN_SUBJECT_CODE = 'subject_code';
    public const COLUMN_SUBJECT_NAME = 'subject_name';
    public const COLUMN_EMAIL = 'email';
    public const COLUMN_MARK = 'mark';
    public const COLUMN_TEACHER_ID = 'teacher_id';

    /** @var int exception codes */
    public const EXCEPTION_UNIQUE = 100;
    public const EXCEPTION_NAME_LENGTH = 200;
}
