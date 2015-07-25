<?php
namespace se;

/**
 * Autogenerated by Thrift Compiler (0.9.2)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
use Thrift\Base\TBase;
use Thrift\Type\TType;
use Thrift\Type\TMessageType;
use Thrift\Exception\TException;
use Thrift\Exception\TProtocolException;
use Thrift\Protocol\TProtocol;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Exception\TApplicationException;


class TagAttr {
  static $_TSPEC;

  /**
   * @var string
   */
  public $name = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'name',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['name'])) {
        $this->name = $vals['name'];
      }
    }
  }

  public function getName() {
    return 'TagAttr';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->name);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('TagAttr');
    if ($this->name !== null) {
      $xfer += $output->writeFieldBegin('name', TType::STRING, 1);
      $xfer += $output->writeString($this->name);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class NumericAttr {
  static $_TSPEC;

  /**
   * @var string
   */
  public $name = null;
  /**
   * @var int
   */
  public $low = null;
  /**
   * @var int
   */
  public $high = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'name',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'low',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'high',
          'type' => TType::I32,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['name'])) {
        $this->name = $vals['name'];
      }
      if (isset($vals['low'])) {
        $this->low = $vals['low'];
      }
      if (isset($vals['high'])) {
        $this->high = $vals['high'];
      }
    }
  }

  public function getName() {
    return 'NumericAttr';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->name);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->low);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->high);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('NumericAttr');
    if ($this->name !== null) {
      $xfer += $output->writeFieldBegin('name', TType::STRING, 1);
      $xfer += $output->writeString($this->name);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->low !== null) {
      $xfer += $output->writeFieldBegin('low', TType::I32, 3);
      $xfer += $output->writeI32($this->low);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->high !== null) {
      $xfer += $output->writeFieldBegin('high', TType::I32, 4);
      $xfer += $output->writeI32($this->high);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class SearchCondition {
  static $_TSPEC;

  /**
   * @var \se\NumericAttr[]
   */
  public $num_filter = null;
  /**
   * @var \se\TagAttr[]
   */
  public $tag_filter = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'num_filter',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => '\se\NumericAttr',
            ),
          ),
        2 => array(
          'var' => 'tag_filter',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => '\se\TagAttr',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['num_filter'])) {
        $this->num_filter = $vals['num_filter'];
      }
      if (isset($vals['tag_filter'])) {
        $this->tag_filter = $vals['tag_filter'];
      }
    }
  }

  public function getName() {
    return 'SearchCondition';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::LST) {
            $this->num_filter = array();
            $_size0 = 0;
            $_etype3 = 0;
            $xfer += $input->readListBegin($_etype3, $_size0);
            for ($_i4 = 0; $_i4 < $_size0; ++$_i4)
            {
              $elem5 = null;
              $elem5 = new \se\NumericAttr();
              $xfer += $elem5->read($input);
              $this->num_filter []= $elem5;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::LST) {
            $this->tag_filter = array();
            $_size6 = 0;
            $_etype9 = 0;
            $xfer += $input->readListBegin($_etype9, $_size6);
            for ($_i10 = 0; $_i10 < $_size6; ++$_i10)
            {
              $elem11 = null;
              $elem11 = new \se\TagAttr();
              $xfer += $elem11->read($input);
              $this->tag_filter []= $elem11;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('SearchCondition');
    if ($this->num_filter !== null) {
      if (!is_array($this->num_filter)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('num_filter', TType::LST, 1);
      {
        $output->writeListBegin(TType::STRUCT, count($this->num_filter));
        {
          foreach ($this->num_filter as $iter12)
          {
            $xfer += $iter12->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    if ($this->tag_filter !== null) {
      if (!is_array($this->tag_filter)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('tag_filter', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->tag_filter));
        {
          foreach ($this->tag_filter as $iter13)
          {
            $xfer += $iter13->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class TagGroup {
  static $_TSPEC;

  /**
   * @var string
   */
  public $name = null;
  /**
   * @var string[]
   */
  public $tag = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'name',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'tag',
          'type' => TType::LST,
          'etype' => TType::STRING,
          'elem' => array(
            'type' => TType::STRING,
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['name'])) {
        $this->name = $vals['name'];
      }
      if (isset($vals['tag'])) {
        $this->tag = $vals['tag'];
      }
    }
  }

  public function getName() {
    return 'TagGroup';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->name);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::LST) {
            $this->tag = array();
            $_size14 = 0;
            $_etype17 = 0;
            $xfer += $input->readListBegin($_etype17, $_size14);
            for ($_i18 = 0; $_i18 < $_size14; ++$_i18)
            {
              $elem19 = null;
              $xfer += $input->readString($elem19);
              $this->tag []= $elem19;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('TagGroup');
    if ($this->name !== null) {
      $xfer += $output->writeFieldBegin('name', TType::STRING, 1);
      $xfer += $output->writeString($this->name);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->tag !== null) {
      if (!is_array($this->tag)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('tag', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRING, count($this->tag));
        {
          foreach ($this->tag as $iter20)
          {
            $xfer += $output->writeString($iter20);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class Catalog {
  static $_TSPEC;

  /**
   * @var int
   */
  public $id = null;
  /**
   * @var string
   */
  public $name = null;
  /**
   * @var \se\TagGroup[]
   */
  public $tag_group = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'id',
          'type' => TType::I32,
          ),
        2 => array(
          'var' => 'name',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'tag_group',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => '\se\TagGroup',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['name'])) {
        $this->name = $vals['name'];
      }
      if (isset($vals['tag_group'])) {
        $this->tag_group = $vals['tag_group'];
      }
    }
  }

  public function getName() {
    return 'Catalog';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->id);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->name);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::LST) {
            $this->tag_group = array();
            $_size21 = 0;
            $_etype24 = 0;
            $xfer += $input->readListBegin($_etype24, $_size21);
            for ($_i25 = 0; $_i25 < $_size21; ++$_i25)
            {
              $elem26 = null;
              $elem26 = new \se\TagGroup();
              $xfer += $elem26->read($input);
              $this->tag_group []= $elem26;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('Catalog');
    if ($this->id !== null) {
      $xfer += $output->writeFieldBegin('id', TType::I32, 1);
      $xfer += $output->writeI32($this->id);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->name !== null) {
      $xfer += $output->writeFieldBegin('name', TType::STRING, 2);
      $xfer += $output->writeString($this->name);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->tag_group !== null) {
      if (!is_array($this->tag_group)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('tag_group', TType::LST, 3);
      {
        $output->writeListBegin(TType::STRUCT, count($this->tag_group));
        {
          foreach ($this->tag_group as $iter27)
          {
            $xfer += $iter27->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class SeResponse {
  static $_TSPEC;

  /**
   * @var int
   */
  public $err_no = null;
  /**
   * @var int
   */
  public $total_num = null;
  /**
   * @var int[]
   */
  public $id = null;
  /**
   * @var \se\SearchCondition
   */
  public $search_condition = null;
  /**
   * @var \se\Catalog
   */
  public $catalog = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'err_no',
          'type' => TType::I32,
          ),
        2 => array(
          'var' => 'total_num',
          'type' => TType::I32,
          ),
        3 => array(
          'var' => 'id',
          'type' => TType::LST,
          'etype' => TType::I64,
          'elem' => array(
            'type' => TType::I64,
            ),
          ),
        4 => array(
          'var' => 'search_condition',
          'type' => TType::STRUCT,
          'class' => '\se\SearchCondition',
          ),
        5 => array(
          'var' => 'catalog',
          'type' => TType::STRUCT,
          'class' => '\se\Catalog',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['err_no'])) {
        $this->err_no = $vals['err_no'];
      }
      if (isset($vals['total_num'])) {
        $this->total_num = $vals['total_num'];
      }
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['search_condition'])) {
        $this->search_condition = $vals['search_condition'];
      }
      if (isset($vals['catalog'])) {
        $this->catalog = $vals['catalog'];
      }
    }
  }

  public function getName() {
    return 'SeResponse';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->err_no);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->total_num);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::LST) {
            $this->id = array();
            $_size28 = 0;
            $_etype31 = 0;
            $xfer += $input->readListBegin($_etype31, $_size28);
            for ($_i32 = 0; $_i32 < $_size28; ++$_i32)
            {
              $elem33 = null;
              $xfer += $input->readI64($elem33);
              $this->id []= $elem33;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRUCT) {
            $this->search_condition = new \se\SearchCondition();
            $xfer += $this->search_condition->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRUCT) {
            $this->catalog = new \se\Catalog();
            $xfer += $this->catalog->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('SeResponse');
    if ($this->err_no !== null) {
      $xfer += $output->writeFieldBegin('err_no', TType::I32, 1);
      $xfer += $output->writeI32($this->err_no);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->total_num !== null) {
      $xfer += $output->writeFieldBegin('total_num', TType::I32, 2);
      $xfer += $output->writeI32($this->total_num);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->id !== null) {
      if (!is_array($this->id)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('id', TType::LST, 3);
      {
        $output->writeListBegin(TType::I64, count($this->id));
        {
          foreach ($this->id as $iter34)
          {
            $xfer += $output->writeI64($iter34);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    if ($this->search_condition !== null) {
      if (!is_object($this->search_condition)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('search_condition', TType::STRUCT, 4);
      $xfer += $this->search_condition->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->catalog !== null) {
      if (!is_object($this->catalog)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('catalog', TType::STRUCT, 5);
      $xfer += $this->catalog->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class SeRequest {
  static $_TSPEC;

  /**
   * @var string
   */
  public $query = null;
  /**
   * @var int
   */
  public $pn = null;
  /**
   * @var int
   */
  public $rn = null;
  /**
   * @var int
   */
  public $type = 1;
  /**
   * @var int
   */
  public $catalog = -1;
  /**
   * @var string[]
   */
  public $tag = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'query',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'pn',
          'type' => TType::I32,
          ),
        3 => array(
          'var' => 'rn',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'type',
          'type' => TType::I32,
          ),
        5 => array(
          'var' => 'catalog',
          'type' => TType::I32,
          ),
        6 => array(
          'var' => 'tag',
          'type' => TType::LST,
          'etype' => TType::STRING,
          'elem' => array(
            'type' => TType::STRING,
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['query'])) {
        $this->query = $vals['query'];
      }
      if (isset($vals['pn'])) {
        $this->pn = $vals['pn'];
      }
      if (isset($vals['rn'])) {
        $this->rn = $vals['rn'];
      }
      if (isset($vals['type'])) {
        $this->type = $vals['type'];
      }
      if (isset($vals['catalog'])) {
        $this->catalog = $vals['catalog'];
      }
      if (isset($vals['tag'])) {
        $this->tag = $vals['tag'];
      }
    }
  }

  public function getName() {
    return 'SeRequest';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->query);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->pn);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->rn);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->type);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->catalog);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::LST) {
            $this->tag = array();
            $_size35 = 0;
            $_etype38 = 0;
            $xfer += $input->readListBegin($_etype38, $_size35);
            for ($_i39 = 0; $_i39 < $_size35; ++$_i39)
            {
              $elem40 = null;
              $xfer += $input->readString($elem40);
              $this->tag []= $elem40;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('SeRequest');
    if ($this->query !== null) {
      $xfer += $output->writeFieldBegin('query', TType::STRING, 1);
      $xfer += $output->writeString($this->query);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->pn !== null) {
      $xfer += $output->writeFieldBegin('pn', TType::I32, 2);
      $xfer += $output->writeI32($this->pn);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->rn !== null) {
      $xfer += $output->writeFieldBegin('rn', TType::I32, 3);
      $xfer += $output->writeI32($this->rn);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->type !== null) {
      $xfer += $output->writeFieldBegin('type', TType::I32, 4);
      $xfer += $output->writeI32($this->type);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->catalog !== null) {
      $xfer += $output->writeFieldBegin('catalog', TType::I32, 5);
      $xfer += $output->writeI32($this->catalog);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->tag !== null) {
      if (!is_array($this->tag)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('tag', TType::LST, 6);
      {
        $output->writeListBegin(TType::STRING, count($this->tag));
        {
          foreach ($this->tag as $iter41)
          {
            $xfer += $output->writeString($iter41);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


