<?php
namespace Pingu\Installation;

use PinguInstaller\Exceptions\DriverNotInstalled;

class DatabaseChecker
{
    /**
     * Checks a database connection
     * 
     * @param  string $driver
     * @param  string $host
     * @param  string $name
     * @param  string $username
     * @param  string $password
     * @return bool
     */
    public static function checkConnection(string $driver, string $host, string $name, string $username, string $password)
    {
        switch ($driver) {
            case 'mysql':
                return static::checkMysql($host, $name, $username, $password);
        }
        return false;
    }

    /**
     * Checks a Mysql connection
     * 
     * @param  string $host
     * @param  string $name
     * @param  string $username
     * @param  string $password
     * @return bool
     */
    public static function checkMysql(string $host, string $name, string $username, string $password)
    {
        if(in_array($host, ['127.0.0.1', 'localhost']) and !function_exists('mysqli_connect')){
            throw new DriverNotInstalled('Mysql driver is not installed');
        }
        $con = mysqli_connect($host, $username, $password, $name);
    }
}