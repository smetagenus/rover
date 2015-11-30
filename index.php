<?php
/**
 * Mars Rover 
 * 
 * Реализация программы-марсохода
 *    
 * @author Belyaev Mihail
*/
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * Класс Полигон
   
class Polygon
{
  public $sizeX = "10";
  public $sizeY = "7";
  
  public function setSize($x,$y) // функция для задания размеров полигона
    {
        $this->sizeX = $x;
        $this->sizeY = $y;
    }
  public function getSize() // функция для получения размеров полигона
    {
        return $this->sizeX . " x " . $this->sizeY;
    }  
}
*/
class Polygon
{
  public static $sizeX = "10";
  public static $sizeY = "10";
  
  public static function setSize($x,$y) // функция для задания размеров полигона
    {
        self::$sizeX = $x;
        self::$sizeY = $y;
    }
  public static function getSizeX() // функция для получения размеров полигона
    {
        return self::$sizeX;
    }  
    public static function getSizeY() // функция для получения размеров полигона
    {
        return self::$sizeY;
    }
}

// Класс Позиция
class Position
{
  public static $posX = "0";
  public static $posY = "0";

  public static function setPos($x,$y) // функция для задания координат позиции
    {
        self::$posX = $x;
        self::$posY = $y;
    }
  public static function getPosX() 
    {
        return self::$posX;
    }  
  public static function getPosY() 
    {
        return self::$posY;
    }    
}

// Класс Ориентация
class Orientation
{
  public static $side = "N";
  
  public static function checkSide($s) // функция для проверки правильности направления
    {
        if ($s == "N" || $s == "S" || $s == "E" || $s == "W") 
          return true; 
        else 
          return false;
    }
  public static function setSide($s) // функция для задания направления
    {
        if (self::checkSide($s))
          self::$side = $s;
    }
  public static function getSide() 
    {
        return self::$side;
    }   
}

/**
 * Класс Движение
 * 
 *   
*/ 

class Moving
{
  /*public static $left = "L";
  public static $right = "R";
  public static $forward = "M";*/
  
  public static function checkMove($x)  // функция проверки команд
  {
    if ($x == "L" || $x == "R" || $x == "M")
      return true;
    else
      return false;
  }
  
  public static function moveTo($x)  // функция движения: определяет команду, и вызывает соответствующий обработчик
  {
    if ($x == "L") self::goLeft();
    if ($x == "R") self::goRight();
    if ($x == "M") self::goForward();
    
    /*if ($x == self::$left) self::goLeft();
    if ($x == self::$right) self::goRight();
    if ($x == self::$forward) self::goForward();*/
  }

  public function goLeft()
  {
    $side = Orientation::getSide();
    if ($side == "N") $newSide = "W";
    if ($side == "W") $newSide = "S";
    if ($side == "S") $newSide = "E";
    if ($side == "E") $newSide = "N";
    Orientation::setSide($newSide);
  }
  
  public function goRight()
  {
    $side = Orientation::getSide();
    if ($side == "N") Orientation::setSide("E");
    if ($side == "W") Orientation::setSide("N");
    if ($side == "S") Orientation::setSide("W");
    if ($side == "E") Orientation::setSide("S"); 
  }
  
  public function goForward()
  {
    $side = Orientation::getSide();
    $x = $newX = Position::getPosX();
    $y = $newY = Position::getPosY();
    if ($side == "N") $newY = $y + 1;
    if ($side == "S") $newY = $y - 1;
    if ($side == "E") $newX = $x + 1;
    if ($side == "W") $newX = $x - 1;
    Position::setPos($newX,$newY);
  }
}


// Класс Парсер
class Parser
{
  public static function parsInput($s) // Парсер входных параметров
  {
    $error = "Неверный ввод данных!<br>";
    $lines = explode("\n", $s); // Получаем массив строк
    if (count($lines) % 2 == 1 && count($lines) > 2)  // Проверяем нечетность
    {
      self::parsPolygon($lines[0]); // Полигон
      
      for ($i = 1; $i < count($lines); $i++) {
        if (($i % 2) == 1)
          self::parsPosition($lines[$i]);    // Координаты ровера
        if (($i % 2) == 0)
          self::parsMoving($lines[$i]);     // Команды для ровера
      }
    } 
    else 
      echo $error;

  }

  public static function parsPolygon($s) // Парсер размеров полигона
  {
    $error = "Неверно заданы размеры полигона!<br>";
    $s = trim($s);
    $size = explode(" ", $s); // получаем массив параметров полигона
    if (count($size) == 2)
    {
      $x = $size[0];
      $y = $size[1];
      if (is_numeric($x) && is_numeric($y))
        Polygon::setSize($x,$y);
      else 
        echo $error;
    } 
    else 
      echo $error;

  }
  
  public static function parsPosition($s) // Парсер координат ровера
  {
    $error = "Неверно заданы координаты ровера!<br>";
    $s = strtoupper(trim($s));
    $pos = explode(" ", $s); // получаем массив параметров ровера 
    if (count($pos) == 3)
    {
      $posX = $pos[0];
      $posY = $pos[1];
      $side = $pos[2];
      if (is_numeric($posX) && is_numeric($posY) && Orientation::checkSide($side))
      {
        Position::setPos($posX,$posY);
        Orientation::setSide($side);
      }
      else 
        echo $error;
      
    }
    else 
      echo $error;
  }
  
  public static function parsMoving($s) // Парсер команд движения ровера
  {
    $error = "Неверно заданы команды для ровера!<br>";
    $s = strtoupper(trim($s));
    $commands = str_split($s);; // получаем массив команд 
    foreach ($commands as $value) {
      if (Moving::checkMove($value))
        Moving::moveTo($value);
      else 
        {
          echo $error;
          echo $value . "<br>";
          break;
        }
    }
  }
}
// ------------------------
//$plateu = new Polygon;
//echo "Стандартный размер полигона ". $plateu->getSize() . "<br>";
//$plateu->setSize(4,5);
//echo "Новый размер полигона ". $plateu->getSize() . "<br>";

//Parser::parsPolygon("33 45 ");


//$pos = new Position;
//Orientation::setSide("E");
//Parser::parsPosition(" 44 123 e");

echo "Позиция ровера : " . Position::getPosX() . " ". Position::getPosY() . " " . Orientation::getSide() . "<br>";
/*Moving::moveTo("M");
Moving::moveTo("M");
Moving::moveTo("L");
Moving::moveTo("M");*/

$_GET['textarea']="10 52
0 0 E
MMLMM";
Parser::parsInput($_GET['textarea']);

echo "Новый размер полигона ". Polygon::getSizeX()  . "x" . Polygon::getSizeY() . "<br>";

//Parser::parsMoving(" mmLM ");

  echo "Позиция ровера : " . Position::getPosX() . " ". Position::getPosY() . " " . Orientation::getSide() . "<br>";
?>