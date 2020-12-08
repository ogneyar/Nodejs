<?

unset($t);
session_start();

$ogl=array(
   "№", "Наим. организации", "Фамилия", "Имя",
   "Отчество", "Юр. адрес", "Факт. адрес", "ИНН",
   "Телефон", "E-mail");

$ogl_zak=array(
   "№ зак", "№ пок", "Наим. орг", "Фамилия", "Наим. тов.",
   "Цена тов.", "Кол. тов.", "Сумма", "ИТОГО:", "Скидка (в %)",
   "ИТОГО(со скидкой):", "Дата заказа", "Время заказа", "Доставлен?");

$ogl_tov=array(
   "№", "Наименование", "Артикул", "Приход(в руб)", "Кол-во(шт)",
   "Резерв(шт)", "Наценка(%)", "Цена(в руб)", "В ассортименте?");

$ogl_t2=array(
   "Наименование", "Артикул", "Приход(в руб)", "Кол-во(шт)",
   "Резерв(шт)", "Наценка(%)", "Цена(в руб)", "Информация",
   "Есть в ассортименте?", "Путь к рисунку");

$ogl_sk=array(
   "№", "Более (в руб)", "Скидка (в %)");


$hostname = "localhost";
$coltab='#eeeedd';
$col='#eeeeee';

include_once "../functions.php";


//--------------------------------------------------//
//------------- основной код модуля admin ----------//
//--------------------------------------------------//

// $c - основная переменная, указывающая на нужное действие
if (!isset($c)) 
{
   $c='';
   // Очистка массива $t
   $k=@array_keys($t);
   for ($i=0; $i<count($k); $i++) 
   {
      unset($t[$k[$i]]);      
   }
}

switch($c) 
{

   // без параметров 
   case "":   
      echo "<TITLE>Ввод пароля</TITLE>";
      
      echo "<BODY BGCOLOR=$col alink=blue vlink=blue>".
           "<table border=1><td BGCOLOR=$coltab>&nbsp;&nbsp;<a href=\>".
           "Главная страница</a>&nbsp;&nbsp;</td>".
           "</table><hr><br><br><br><center>".
           "<FONT color=#9999dd size=5>Введите пароль!".
           "</FONT></center><br><br><br><br>";

      ?>
           <center><TABLE WIDTH = 400><TR><TD align = center>
           <form action= ?c=adm&SID=$SID&login=$login&pass=$pass method=POST>Логин:<BR>
           <input type="text" NAME="login" SIZE="15"><BR>Пароль:<BR>
           <input type="password" NAME="pass" SIZE="15">
           <br><input type="submit" VALUE="Вход!">
           </form></td></tr></TABLE></center>
      <?
      
   break;


   case "adm":   
      echo "<TITLE>Администраторская</TITLE>";
      
      $t[0][log]=$login;
      $t[0][pas]=$pass;
       
      session_register("t");

      echo "<BODY BGCOLOR=$col alink=blue vlink=blue>".
           "<table border=1><td BGCOLOR=$coltab>&nbsp;&nbsp;<a href=\>".
           "Главная страница</a>&nbsp;&nbsp;</td><td BGCOLOR=$coltab>".
           "&nbsp;&nbsp;&nbsp;&nbsp;<a href= ?c=&SID=$SID>Ввод пароля</a>".
           "&nbsp;&nbsp;&nbsp;&nbsp;</td></table><hr><br><br><br><center>".
           "<FONT color=#9999dd size=5>Администраторская".
           "</FONT></center><br><br>";
      admin();
   break;



   case "nazad":   
      echo "<TITLE>Администраторская</TITLE>".
           "<BODY BGCOLOR=$col alink=blue vlink=blue>".
           "<table border=1><td BGCOLOR=$coltab>&nbsp;&nbsp;<a href=\>".
           "Главная страница</a>&nbsp;&nbsp;</td><td BGCOLOR=$coltab>".
           "&nbsp;&nbsp;&nbsp;&nbsp;<a href= ?c=&SID=$SID>Ввод пароля</a>".
           "&nbsp;&nbsp;&nbsp;&nbsp;</td>".
           "</table><hr><br><br><br><center>".
           "<FONT color=#9999dd size=5>Администраторская".
           "</FONT></center><br>";

      ?>   <center><table border=2><td BGCOLOR=white>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <a href= ?c=tov&SID=$SID>Данные о товаре</a>&nbsp;&nbsp;&nbsp;&nbsp;
           &nbsp;</td><td BGCOLOR=white>&nbsp;&nbsp;&nbsp;&nbsp;
           &nbsp;<a href= ?c=zakaz&SID=$SID>Данные о заказах</a>
           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td BGCOLOR=white>
           &nbsp;&nbsp;&nbsp;<a href= ?c=infop&SID=$SID>
           Данные о покупателях</a>&nbsp;&nbsp;&nbsp;</td>
           <td BGCOLOR=white>&nbsp;&nbsp;&nbsp;
           <a href= ?c=infoskid&SID=$SID>Данные о скидках</a>&nbsp;
           &nbsp;&nbsp;</td></table></center><br><br>

           <center><table border=2><td BGCOLOR=white>&nbsp;
           <a href= ?c=dob&SID=$SID>Добавление данных в БД</a>&nbsp;</td>
           <td BGCOLOR=white>&nbsp;
           <a href= ?c=uvel&SID=$SID>Увеличение кол-ва товара</a>&nbsp;</td></center>
      <?
   break;


   // Данные о товаре
   case "tov":
      echo "<TITLE>Данные о товаре</TITLE>".           
           "<BODY BGCOLOR=$col alink=blue vlink=blue>".
           "<table border=1><td BGCOLOR=$coltab>&nbsp;&nbsp;<a href=\>".
           "Главная страница</a>&nbsp;&nbsp;</td><td BGCOLOR=$coltab>".
           "&nbsp;<a href= ?c=nazad&SID=$SID>Администраторская</a>&nbsp;".
           "</td></table><hr><center><FONT color=#9999dd size=5>".
           "Данные о товаре</FONT></center><br>";
      infotov();
   break;


   case "zakaz":  
      echo "<TITLE>Данные о заказах</TITLE>".           
           "<BODY BGCOLOR=$col alink=blue vlink=blue>".
           "<table border=1><td BGCOLOR=#eeeedd>&nbsp;&nbsp;<a href=\>".
           "Главная страница</a>&nbsp;&nbsp;</td><td BGCOLOR=$coltab>".
           "&nbsp;<a href= ?c=nazad&SID=$SID>Администраторская</a>&nbsp;".
           "</td></table><hr><center><FONT color=#9999dd size=5>".
           "Данные о заказах</FONT></center><br>";
      zakaz();
   break;  


   // Выводится ИНФормация О Покупателях из таблицы "pokup"
   case "infop":  
      echo "<TITLE>Данные о покупателях</TITLE>".           
           "<BODY BGCOLOR=$col alink=blue vlink=blue>".
           "<table border=1><td BGCOLOR=#eeeedd>&nbsp;&nbsp;<a href=\>".
           "Главная страница</a>&nbsp;&nbsp;</td><td BGCOLOR=$coltab>".
           "&nbsp;<a href= ?c=nazad&SID=$SID>Администраторская</a>&nbsp;".
           "</td></table><hr><center><FONT color=#9999dd size=5>".
           "Данные о покупателях</FONT></center><br>";
      infop();
   break;  


   // Информация о скидках
   case "infoskid":
      echo "<TITLE>Данные о скидках</TITLE>".           
           "<BODY BGCOLOR=$col alink=blue vlink=blue>".
           "<table border=1><td BGCOLOR=#eeeedd>&nbsp;&nbsp;<a href=\>".
           "Главная страница</a>&nbsp;&nbsp;</td><td BGCOLOR=$coltab>".
           "&nbsp;<a href= ?c=nazad&SID=$SID>Администраторская</a>&nbsp;".
           "</td></table><hr><center><FONT color=#9999dd size=5>".
           "Данные о скидках</FONT></center><br>";
      insk();      
   break;


   // Добавление данных в БД
   case "dob":
      echo "<TITLE>Добавление данных в БД</TITLE>".           
           "<BODY BGCOLOR=$col alink=blue vlink=blue>".
           "<table border=1><td BGCOLOR=$coltab>&nbsp;&nbsp;<a href=\>".
           "Главная страница</a>&nbsp;&nbsp;</td><td BGCOLOR=$coltab>".
           "&nbsp;<a href= ?c=nazad&SID=$SID>Администраторская</a>&nbsp;".
           "</td></table><hr><center><FONT color=#9999dd size=5>".
           "Добавление данных в БД</FONT></center><br>";

      echo "<center><form action='$PHP_SELF?c=dobpov&SID=$SID&v=$v' method=post>".
           "<table border=1>";
      for ($i=0; $i<count($ogl_t2); $i++) 
      {
         echo "<tr><td BGCOLOR=$coltab>$ogl_t2[$i]</td>".
              "<td><input type=text size=32 name='v[$i]'></td></tr>";
      }
      echo "</table>";

      echo "<input type=submit value='Добавить'>".
           "</form></center>";

      
   break;


   case "dobpov":
      dob();
      exit(header("Location: $PHP_SELF?c=nazad&SID=$SID"));
   break;


   case "del":      
      if ((!isset($da))&&(!isset($net)))
      {
         echo "<BODY BGCOLOR=$col>";
         sure("del");         
      }
      if (isset($da))
      {
         delete($id);
         exit(header("Location: $PHP_SELF?c=tov&SID=$SID"));
      }
      if(isset($net)) exit(header("Location: $PHP_SELF?c=tov&SID=$SID"));
      unset($da);
      Unset($net);
   break;


   case "asort":
      asortiment($id);
      exit(header("Location: $PHP_SELF?c=tov&SID=$SID"));
   break;


   case "uvel":
      echo "<TITLE>Увеличение количества товара</TITLE>".           
           "<BODY BGCOLOR=$col alink=blue vlink=blue>".
           "<table border=1><td BGCOLOR=$coltab>&nbsp;&nbsp;<a href=\>".
           "Главная страница</a>&nbsp;&nbsp;</td><td BGCOLOR=$coltab>".
           "&nbsp;<a href= ?c=nazad&SID=$SID>Администраторская</a>&nbsp;".
           "</td></table><hr><center><FONT color=#9999dd size=5>".
           "Увеличение количества товара</FONT></center><br>";
      Uvel();
   break;


   // Изменение количества товара
   case "izm":
      izmkol();
      exit(header("Location: $PHP_SELF?c=uvel&SID=$SID"));
   break;


   // Удаление строки из таблицы skid
   case "delsk":
      if ((!isset($da))&&(!isset($net)))
      {
         echo "<BODY BGCOLOR=$col>";
         sure("delsk");         
      }
      if (isset($da))
      {
         dlsk($id);
         exit(header("Location: $PHP_SELF?c=infoskid&SID=$SID"));
      }
      if(isset($net)) exit(header("Location: $PHP_SELF?c=infoskid&SID=$SID"));
      unset($da);
      Unset($net);      
   break;


   // Добавление строки в таблицу skid
   case "dobvsk":
      dbsk($v1,$v2);
      exit(header("Location: $PHP_SELF?c=infoskid&SID=$SID"));      
   break;


}

?>