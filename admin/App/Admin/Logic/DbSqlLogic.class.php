<?php
/**
 *继承接口 并重写接口方法
 */

namespace Admin\Logic;

class DbSqlLogic implements DbMysqlInterface
{

    /**
     * DB connect
     *
     * @access public
     *
     * @return resource connection link
     */
    public function connect ()
    {
        dump (func_get_args ());
        echo  __METHOD__;

    }

    /**
     * Disconnect from DB
     *
     * @access public
     *
     * @return viod
     */
    public function disconnect ()
    {
        dump (func_get_args ());
        echo  __METHOD__;
    }

    /**
     * Free result
     *
     * @access public
     * @param resource $result query resourse
     *
     * @return viod
     */
    public function free ($result)
    {
        dump (func_get_args ());
        echo  __METHOD__;
    }

    /**
     * Execute simple query
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return resource|bool query result
     */
    public function query ($sql, array $args = array())
    {
        $data=func_get_args ();
        //获取sql语句
        $sql=array_shift ($data);
        //找出所有sql语句占位符 使用正则分割
        $sqlrows=preg_split ('/\?[FNT]/',$sql);
        $sql='';
        //循环为把值替换到sql语句上
        foreach ($sqlrows as $key=>$value){
            $sql.=$value.$data[$key];
        }


//        //获取数据并返回
        return M('GoodsCategory')->execute ($sql);

    }

    /**
     * Insert query method
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return int|false last insert id
     */
    public function insert ($sql, array $args = array())
    {
        $data=func_get_args ();

        //获取sql语句
        $sql=array_shift ($data);
        //获取表名
        $table=array_shift ($data);
        //获取数据
        $data=array_shift ($data);
        //找出所有sql语句占位符 使用正则分割
        $sqlrows=preg_split ('/\?[FNT\%]/',$sql);
        //循环为把值替换到sql语句上
          $keys=array_keys ($data);
          $values=array_values ($data);

        $str='';
        foreach ($keys as $key=>$value){
          $str.="{$value}="."'{$values[$key]}',";
        }
          $arr=explode (",",$str);
           array_pop($arr);
           $str=implode (',',$arr);

        $sql="INSERT INTO {$table} SET  {$str}";

//
//        //插入数据并返回
        return M('GoodsCategory')->execute ($sql);

    }

    /**
     * Update query method
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return int|false affected rows
     */
    public function update ($sql, array $args = array())
    {
        dump (func_get_args ());
        echo  __METHOD__;
    }

    /**
     * Get all query result rows as associated array
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array (two level array)
     */
    public function getAll ($sql, array $args = array())
    {
        dump (func_get_args ());
        echo  __METHOD__;
    }

    /**
     * Get all query result rows as associated array with first field as row key
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array (two level array)
     */
    public function getAssoc ($sql, array $args = array())
    {
        dump (func_get_args ());
        echo  __METHOD__;
    }

    /**
     * Get only first row from query
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array
     */
    public function getRow ($sql, array $args = array())
    {
        $data=func_get_args ();
        //获取sql语句
        $sql=array_shift ($data);
        //找出所有sql语句占位符 使用正则分割
        $sqlrows=preg_split ('/\?[FNT]/',$sql);
        $sql='';
        //循环为把值替换到sql语句上
        foreach ($sqlrows as $key=>$value){
           $sql.=$value.$data[$key];
        }

       //获取数据 返回的是二位数组
       $rows=M('GoodsCategory')->query ($sql);
        //只需要获取一条数据
        //把二位数据压缩出一维数组
        return array_shift ($rows);
    }

    /**
     * Get first column of query result
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array one level data array
     */
    public function getCol ($sql, array $args = array())
    {
        dump (func_get_args ());
        echo  __METHOD__;
    }

    /**
     * Get one first field value from query result
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return string field value
     */
    public function getOne ($sql, array $args = array())
    {


        $date=func_get_args ();

        $sql="SELECT MAX({$date[1]}) FROM  {$date[2]}";

       return $this->query ($sql);
    }
}