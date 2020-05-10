<?php 

namespace Pingu\Installation\Contracts;

interface RequirementCheckerContract
{
    /**
     * Checks all requirements
     * 
     * @return array
     */
    public function checkAll(): array;

    /**
     * Has at least one requirement failed
     * 
     * @return boolean
     */
    public function hasFailed(): bool;

    /**
     * Checks php min version
     */
    public function checkPhp();

    /**
     * Checks that apache can write on its home folder (usually /var/www), it will need it to run npm
     */
    public function checkApacheFolderPermissions();

    /**
     * Checks php extensions
     */
    public function checkPhpExtensions();

    /**
     * Checks apache extensions
     */
    public function checkApacheExtensions();

    /**
     * Checks folder permissions
     */
    public function checkPermissions();

    /**
     * Checks npm version
     */
    public function checkNpm();
}