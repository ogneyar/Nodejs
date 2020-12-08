<?php

unset($t);
session_start();


$post=array(
       "Наименование организации",
       "Фамилия",
       "Имя",
       "Отчество",      
       "Юридический адрес",
       "Фактический адрес",
       "ИНН",
       "Контактный телефон",
       "E-mail");
$ogl = array(
       "№",
       "Наименование",
       "Цена(в руб)",
       "Цена со скидкой(в руб)",
       "На складе");
$hostname = "localhost";
$username = "root";
$password = "";
$dbName = "shop";
$usertable="tovar";
$coltab='#eeeedd';
$col='#eeeeee';

include_once "../functions.php";


//--------------------------------------------------//
//------------- основной код модуля shop -----------//
//--------------------------------------------------//

if(isset($_GET['c'])) $c = $_GET['c'];

//  // $c - основная переменная, указывающая на нужное действие
if ( !isset($c) ) {
   $c = '';
   // Очистка массива $t
   $k = @array_keys( $t[all] );
   for ( $i = 0; $i < count($k); $i++ ) 
   {
      unset( $t[$k[$i]] );
      unset( $t[all][$k[$i]] );
   }
}

switch($c) 
{

   // без параметров - рисуем прайс-лист
   case "":   
      echo "<TITLE>Интернет магазин</TITLE>";
     //  summa(); // статистика по корзине
      echo "<center><FONT color=red size=5>Каталог</FONT></center>";
      echo "<table border=1><td BGCOLOR=$coltab>&nbsp;&nbsp;".
           "<a href='\'>Главная страница</a>&nbsp;&nbsp;</td></table>";      
      price(); // прайс      
   break;

   case "katal":   
      echo "<TITLE>Интернет магазин</TITLE>";
      // summa(); // статистика по корзине
      echo "<center><FONT color=red size=5>Каталог</FONT></center>";
      echo "<table border=1><td BGCOLOR=$coltab>&nbsp;&nbsp;".
           "<a href='\'>Главная страница</a>&nbsp;&nbsp;</td>";  
      // ссылка для перехода на корзину
      echo "<td BGCOLOR=$coltab>&nbsp;&nbsp;<a href='$PHP_SELF?c=korzina&SID=$SID'>".
           "Просмотреть корзину</a></td></table>";
      price(); // прайс      
   break;

   // вывод корзины
   case "korzina":
      echo "<TITLE>Корзина</TITLE>";
      // summa();
      echo "<center><FONT color=blue size=5>Корзина:</FONT></center>";
      echo "<table border=1><td BGCOLOR=$coltab>&nbsp;&nbsp;<a href='\'>Главная страница</a>".
           "&nbsp;&nbsp;<td BGCOLOR=$coltab>&nbsp;&nbsp;".
           "<a href='$PHP_SELF?c=katal&SID=$SID'>Каталог товаров</a>&nbsp;&nbsp;</td></table>";
      // korzina(); // рисуем таблицу корзины           
   break;


   // добавление из формы прайса всех товаров
   case "add":  
      // в массиве $v скоплены номера строк товаров, которые функция ...

      // $k=@array_keys($v);
      // for ($i=0; $i<count($k); $i++) 
      // {
      //    // ... tadd() преобразует из файла в данные и поместит в сессии
      //    tadd($v[$k[$i]]);
      // }

      // надо перенаправить браузер на приличный адрес, чтобы:
      // 1) в URL был написан приличный адрес
      // 2) чтобы не было глюка, если посетитель нажмет ОБНОВИТЬ СТРАНИЦУ
      
      // exit(header("Location: $PHP_SELF?c=korzina&SID=$SID"));      
   break;


   // измение кол-ва товаров
   case "kolvo":
      // когда на странице КОРЗИНА нажимают СОХРАНИТЬ
      // ИЗМЕНЕНИЯ или ОФОРМИТЬ ЗАКАЗ..

      // $k=@array_keys($v);
      // for ($i=0; $i<count($k); $i++) 
      // {
      //    $t[$k[$i]][kol]=abs(intval($v[$k[$i]]));
      //    if ($t[$k[$i]][kol]>$t[$k[$i]][sklad]) $t[$k[$i]][kol]=$t[$k[$i]][sklad];
      // }

      // после изменения переенной сессии ее нужно записать
      
      // session_register("t");

      // Далее важная проверка. Если посетитель нажимает кнопку СОХРАНИТЬ, то
      // у нас устанавливается переменная $edit, которая содержит строку
      // "Сохранить изменения". Если он нажимает ЗАКАЗ, то устанавливается
      // $zakaz. Устанавливается только одна из этих твух переменных.
      // если это было ИЗМЕНИТЬ, то переправить на корзину
      
      // if (isset($edit)) exit(header("Location: $PHP_SELF?c=korzina&SID=$SID"));

      // иначе переправить на страницу с офрмлением заказа

      // if (isset($zakaz))exit(header("Location: $PHP_SELF?c=zakaz&SID=$SID"));      
   break;


   // удаление товара по его $id
   case "del":
      // $id=intval($id);
      // unset($t[$id]);
      // unset($t[all][$id]);
      // session_register("t");
      // exit(header("Location: $PHP_SELF?c=korzina&SID=$SID"));
   break;


   // удаление всей корзины
   case "delete":
      // Так же как и в пред. пункте, только с проходом
      // массива id товаров

      // $k=@array_keys($t[all]);
      // for ($i=0; $i<count($k); $i++) 
      // {
      //    unset($t[$k[$i]]);
      //    unset($t[all][$k[$i]]);
      // }
      // session_register("t");
      // exit(header("Location: $PHP_SELF?c=korzina&SID=$SID"));
   break;


   // форма для оформления заказа
   case "zakaz":
      //summa();
      echo "<TITLE>Анкета</TITLE>".
           "<BODY BGCOLOR=$col><center><FONT color=green size=5>Анкета</FONT></center>".
           "<table border=1><td BGCOLOR=$coltab>&nbsp;&nbsp;<a href='\'>Главная страница</a>".
           "&nbsp;&nbsp;<td BGCOLOR=$coltab>&nbsp;&nbsp;".
           "<a href='$PHP_SELF?c=korzina&SID=$SID'>Вернуться к корзине</a>&nbsp;&nbsp;</td></table>".
           "<center><form action='$PHP_SELF?c=post&SID=$SID' method=post>".
           "<table border=1>";
      for ($i=0; $i<count($post); $i++) 
      {
         echo "<tr><td BGCOLOR=$coltab>$post[$i]</td>".
              "<td><input type=text size=32 name='v[$i]'></td></tr>";
      }
      echo "</table>";
      echo "<table border=1>".
           "<tr><td>Адрес доставки</td>".
           "<td><input type=text size=32 name='v[$i]'></td></tr>".
           "</table>";
      $i++;

      echo "<table border=1>";
      echo "<tr><td>Дата доставки</td>".
           "<td><input type=text size=1 name='v[$i]'></td>";   
      $i++;
      echo "<td><input type=text size=1 name='v[$i]'></td>";
      $i++;
      echo "<td><input type=text size=1 name='v[$i]'></td>".
           "</tr></table>";
      echo "<table border=1>";
      $i++;
      echo "<tr><td>Время:     с</td>".
           "<td><input type=text size=1 name='v[$i]'></td>";
      $i++;
      echo "<td><input type=text size=1 name='v[$i]'></td>";     
           "</tr>";
      $i++;
      echo "<tr><td>Время:     до</td>".
           "<td><input type=text size=1 name='v[$i]'></td>";
      $i++;
      echo "<td><input type=text size=1 name='v[$i]'></td>".      
           "</tr></table><br>";

      echo "<input type=submit value='Отправить заказ'>".
           "</form></center>";           
   break;


   case "post":
      // dvBD();
      // exit(header("Location: $PHP_SELF?c=anketa&SID=$SID"));
   break;


   case "view":
      echo "<TITLE>Данные о товаре</TITLE>";    
      echo"<BODY BGCOLOR=$col>";  
      echo "<FONT color=blue size=5>Просмотр товара:</FONT><br><br>";
      echo "<table border=1><td BGCOLOR=$coltab>&nbsp;&nbsp;".
           "<a href='\'>Главная страница</a>&nbsp;&nbsp;</td>".
           "<td BGCOLOR=$coltab>&nbsp;&nbsp;".
           "<a href='$PHP_SELF?c=katal&SID=$SID'>Катоалог товаров</a>".
           "&nbsp;&nbsp;</td></table>";
      // view($n);            
   break;


   case "anketa":     
      echo "<BODY BGCOLOR=$col>".
           "<table width=100% height=95%><tr><td align=center>".
           "<h2>Ваш заказ отправлен!<br>".
           "Спасибо за покупку!</h2>".
           "<hr width=60%><br>".
           "<a href='/'>Главная страница</a><br><br>".
           "<a href='".$PHP_SELF."'>Каталог товаров</a>".
           "</tD></tr></table>";
  
  
      $msg="Анкета посетителя:\n\n";
      // for ($i=0; $i<count($post); $i++) 
      // {
      //    $msg.="$post[$i]: ".substr($v[$i],0,500)."\n";
      // }
      // $msg.="\nСписок покупок:\n\n";
      // $k=@array_keys($t[all]);
      // for ($i=0; $i<count($k); $i++) 
      // {
      //    $id=$k[$i];
      //    $msg.=($i+1).") {$t[$id][name]} \\ ".doubleval($t[$id][cena]).
      //          " руб \\ {$t[$id][kol]} шт. \\ = ".
      //          sprintf("%.2f",$t[$id][cena]*$t[$id][kol])." руб\n";
      // }   
      // $timeT=getdate();
      // $msg.="\n\nДата заказа:   ".$timeT[mday].
      //       ".".$timeT[mon].".".$timeT[year]."г.";
      // $msg.="\nВремя заказа:  ".$timeT[hours].
      //       ":".$timeT[minutes].":".$timeT[seconds];         
    
      // echo "<br><br><br><pre>".$msg."</pre>"; 
   break;

}  

?>
