<?php

$mysqli = new mysqli($hostname, $username, $password, $dbName);
if (mysqli_connect_errno()) {	
   echo "<br><br><br><br><br><center>Ошибка! Нет подключения к БД.<br></center>"; 		
   exit;  	
}


/*
tadd
korzina
price
*/

/* функция прибавляет в корзину новый товар, где $n - это номер строки
   в БД MySQL. Далее, в сессиях сохраняется не номер строки, а число
   ID и используется повсеместно. Если товар уже 
   существует, то корзина никак не меняется.   */

//-------------
function tadd($n) 
{   
   // global $t, $hostname, $username, $password, $dbName, $usertable;
   global $t, $usertable;

   global $mysqli;
   

   // mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
   // mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");

   $query="select * from $usertable";
   // $f=mysql_query($query);

   
   $результат = $mysqli->query($query);
   if ($результат) $количество = $результат->num_rows;
   if($количество > 0) $arrayResult = $результат->fetch_all();

   // $id=mysql_result($f,$n,0);

   $id = $arrayResult[$n][0];

   if (isset($t[all][$id])) return; // если товар уже в корзине - выход

   $t[all][$id] = $id;    // флаг, определяющий что товар уже есть в корзине
   //достаём наименование из БД
   // $t[$id][name]=convert_cyr_string(mysql_result($f,$n,1),"d","w");
   // $t[$id][cena]=mysql_result($f,$n,7);
   // $t[$id][sklad]=mysql_result($f,$n,4);
   // $t[$id][info]=mysql_result($f,$n,8);

   $t[$id][name] = $arrayResult[$n][1];
   $t[$id][cena] = $arrayResult[$n][7];
   $t[$id][sklad] = $arrayResult[$n][4];
   $t[$id][info] = $arrayResult[$n][8];

   $t[$id][kol] = 1;      // кол-во в начале равно "1 штуке"

   session_register("t"); // записали переменную в сессию

   // mysql_close();

   $mysqli->close();
}
//-------------

//------------- Рисует таблицу с товарами в корзине. 
function korzina() 
{
   global $t, $PHP_SELF, $SID, $ogl, $coltab, $F_sklad, $skid, $summ2;
   global $summ, $sm;
   // проходим массив $t[all] по списку его ключей
   $k = @array_keys( $t[all] );
   if (count($k) > 0)
   {      
      echo "<br><FONT color=red>КОЛИЧЕСТВО</FONT> товара ".
           "не вводите больше чем есть на складе!!!  ".
           "(при изменении количества нажмите 'внести изменения')";
      echo "<br><FONT color=red>~</FONT> -приближённое значение (скидка считается от общей суммы заказа)";
      echo "<form action=$PHP_SELF?c=kolvo&SID=$SID method=POST>".
        // рисуем заголовок таблицы с корзиной:
           "<table border=2><tr align=center><th BGCOLOR=$coltab>$ogl[0]</th>".
           "<th BGCOLOR=$coltab>$ogl[1]</th>".
           "<th BGCOLOR=$coltab>На складе</th>".
           "<th BGCOLOR=$coltab>$ogl[2]</th>".
           "<th BGCOLOR=$coltab>$ogl[3]</th>".
           "<th BGCOLOR=$coltab>Кол-во</th>".
           "<th BGCOLOR=$coltab>Сумма(в руб)</th>".
           "<th BGCOLOR=$coltab>Сумма со скидкой(в руб)</th>".
           "<th BGCOLOR=$col>&nbsp</th></tr>";    
      $n = 1;  
      for ($i=0; $i<count($k); $i++) 
      {
         $id = $k[$i];
         $S = $t[$id][cena] * $t[$id][kol];
         $sk = $t[$id][cena] / 100 * $skid;
         $sk = $t[$id][cena] - $sk;

         //Округление до сотых
         $sk = $sk * 100;
         $sk = round($sk) / 100;

         $Ssk = $sk * $t[$id][kol];

         //Округление до сотых
         $Ssk = $Ssk * 100;
         $Ssk = round($Ssk) / 100;

         echo "<tr align=center><td BGCOLOR=$coltab>$n</td>".
              "<td BGCOLOR=$coltab>{$t[$id][name]}</td>".
              "<td BGCOLOR=$coltab>{$t[$id][sklad]}</td>".
              "<td BGCOLOR=$coltab>{$t[$id][cena]}</td>".         
              "<td BGCOLOR=$coltab>~ $sk</td>".
              "<td BGCOLOR=$coltab>".
              "<input size=4 type=text name=v[$id] value={$t[$id][kol]}></td>".
              "<td BGCOLOR=$coltab>{$S}</td>".
              "<td BGCOLOR=$coltab>~ $Ssk</td>".
              "<td BGCOLOR=$col>".
              "<a href=$PHP_SELF?c=del&id=$id&SID=$SID>удалить</a></td></tr>";
         $n = $n + 1;
      }

      echo "<tr align=center><td BGCOLOR=$coltab>&nbsp;</td>".
           "<th BGCOLOR=$coltab>ИТОГО:</th>".
           "<td BGCOLOR=$coltab>&nbsp;</td>".
           "<td BGCOLOR=$coltab>&nbsp;</td>".         
           "<td BGCOLOR=$coltab>&nbsp;</td>".
           "<td BGCOLOR=$coltab>$summ2</td>".
           "<td BGCOLOR=$coltab>$summ</td>".
           "<th BGCOLOR=$coltab>".sprintf("<FONT color=red>%.2f</FONT>",$sm)."</th>".
           "<td BGCOLOR=$col>&nbsp;</td></tr>"; 

      // внизу таблицы две кнопки:
      //   Измениения - сохранить изменение числа товаров и обновить страницу
      //   Заказ - сохр. изм. + перейти на страницу оформления заказа
      echo "</table><center><input type=submit name=zakaz value='Оформить заказ'> &nbsp;".
           "&nbsp;<input type=submit name=edit value='Внести изменения'></center></form>";

      echo "<a href='$PHP_SELF?c=delete&SID=$SID'>Очистить корзину!</a><br>";
           
    }else echo "<br><br><br>КОРЗИНА ПУСТАЯ<br><br><br>";
}
//-------------



/* Выводит на экран таблицу с товарами. В таблице автоматом генериться новая
   колонка с checkbox'асами, отметив которые и нажав "добавив", можно
   занести товары в корзину.   */

//-------------   СДЕЛАЛ!!!

function price() {
   global $t, $SID, $ogl, $coltab, $skid;   
   global $mysqli, $usertable;   


   $query = "SELECT naim, cena, kolvo, asort FROM {$usertable}";
   
   $результат = $mysqli->query($query);
   
   if ($результат) $количество = $результат->num_rows;
   else echo "нет результата<br>";

   if($количество > 0) $arrayResult = $результат->fetch_all();
   else echo "нет количества<br>";

   $x = count($ogl);          // вычисляем число колонок
   $y = $количество;

   echo "<br><center>";   
   // форма (не забываем вписать $SID) + начало таблицы:
   echo "<form method=GET><input type=hidden name=c value=add>".
        "<input type=hidden name=SID value=SID><table border=2>";

   // рисуем заголовок таблицы
   echo "<tr>";
   for ($j = 0; $j < $x; $j++) 
   {
      if (strlen($ogl[$j]) == 0) echo "<th BGCOLOR=$coltab>&nbsp;</th>";
      else echo "<th BGCOLOR=$coltab>$ogl[$j]</th>";
   }
   // рисуем колонку просмотр
   echo "<th BGCOLOR=$coltab>&nbsp;</th>";

   // рисуем колонку, где будут checkbox'ы
   echo "<th BGCOLOR=$coltab>'x'</th></tr>";

   $n = 0;
   // основной цикл вывода прайса
   for ($i = 0; $i < $y; $i++) {

      $as = $arrayResult[$i][3];
     
      if ($as == true)
      {         
         $a[] = $n + 1;
         $a[] = $arrayResult[$i][0];
         $a[] = $arrayResult[$i][1];         

         $c = $arrayResult[$i][1];

         $c2 = $c / 100 * $skid;
         $c = $c - $c2;

         //Округление до сотых
         $c = $c * 100;
         $c = round($c) / 100;

         $a[] = $c;

         $a[] = $arrayResult[$i][2];
                  
         if (count($a)<2) continue; // если она пустая (глюки), пропускаем

         echo "<tr ALIGN=center>";

         // цикл вывода всех колонок текущей строки таблицы
         for ($j = 0; $j < $x; $j++) 
         {
            // если ячейка пустая, там надо поместить "&nbsp;";
            if (strlen($a[$j]) == 0) echo "<td BGCOLOR=$coltab>&nbsp;</td>";               
            else echo "<td BGCOLOR=$coltab>$a[$j]</td>";
         }

         // рисуем колонку просмотр
         echo "<td BGCOLOR=$coltab>".               
               "<a href=?c=view&SID=SID&n=$i>Просмотр</a></td>";


         //если нет на складе, то не выводить checkbox        
         if ($arrayResult[$i][2] < 1)
         {
            echo "<td BGCOLOR=$coltab>&nbsp;</td>";
         }
         else // рисуем checkbox 
         {           
            echo "<td BGCOLOR=$coltab><input type=checkbox name=v[$i] value=$i></td>";
         }

         echo "</tr>";
         unset($a);
         
         $n = $n + 1;
      }
   }
   echo "</table><br><button type=submit>".
        "Добавить в корзину</button></form></center>"; 

  
   $mysqli->close();
}
//-------------























/* Выводит на экран несколько чисел (написано). Подсчет значений происходит
   при каджом вызове.   */

//-------------
// function summa() 
// {
//    global $t ,$col,$summ, $sm;
//    global $hostname, $username, $password, $dbName, $skid, $summ2;
//    echo"<BODY BGCOLOR=$col>";
//    // традиционный проход массива товаров из корзины
//    $k=@array_keys($t[all]);
//    $summ2=0; 
//    for ($i=0; $i<count($k); $i++) 
//    {
//       $id=$k[$i];
      
//       $summ+=(double)$t[$id][kol]* (double)$t[$id][cena];
//       $summ2+=$t[$id][kol];
//    }

//    $usertable="skid";
//    mysql_connect($hostname,$username,$password) OR DIE("Не могу создать соединение ");
//    mysql_select_db("$dbName") or die("Не могу выбрать базу данных "); 
//    $q="select * from $usertable";
//    $r=mysql_query($q);
//    $kl=mysql_num_rows($r);
//    $skid=0;
//    for ($j=0; $j<$kl; $j++)
//    {
//       $svishe=convert_cyr_string(mysql_result($r,$j,1),"w","d");
//       $sk=convert_cyr_string(mysql_result($r,$j,2),"w","d");
//       if ($summ>$svishe) $skid=$sk;
//    }
//    $su=$summ/100*$skid;
//    $sm=$summ-$su;

//    //Округление до сотых
//    $sm=$sm*100;
//    $sm=round($sm)/100;

//    $usertable = "tovar";

//    // просто выводим посчитанные цифры на экран
//    echo "<center><FONT color=black> ".
//         "Выбраных товаров: <FONT color=red>$i</FONT> шт.".
//         " Общее количество: <FONT color=red>$summ2</FONT> шт. Сумма: ".
//         sprintf("<FONT color=red>%.2f</FONT> руб.",$summ).
//         " Скидка: <FONT color=red>$skid</font> %".
//         " Сумма (со скидкой): ".
//         sprintf("<FONT color=red>%.2f</FONT> руб.",$sm).
//         "</FONT><hr color=blue></center>";
//    mysql_close();
// }
// //-------------


// // добавление данных в таблицы
// //-------------
// function dvBD() 
// {
//    global $v, $t;
//    global $hostname, $username, $password, $dbName;
//    $usertable = "pokup";
//    /* создать соединение */
//    mysql_connect($hostname,$username,$password) OR DIE("Не могу создать соединение ");
//    mysql_select_db("$dbName") or die("Не могу выбрать базу данных "); 
 
//    for ($j=0; $j<19; $j++)
//    {
//       $Dv[]=convert_cyr_string($v[$j],"w","d");
//    }

//    /* Вставить информацию о клиенте в таблицу pokup*/
//    $query="INSERT INTO $usertable set naim_org='$Dv[0]',".
//           "fam='$Dv[1]',imya='$Dv[2]',otch='$Dv[3]',yur_adress='$Dv[4]',".
//           "fak_adress='$Dv[5]',inn='$Dv[6]',tel='$Dv[7]',email='$Dv[8]'";
//    $result = mysql_query($query);

//    $q="select id from $usertable";
//    $r=mysql_query($q);
//    $n2=mysql_num_rows($r);
//    $num=convert_cyr_string(mysql_result($r,$n2-1,0),"d","w");


//    $k=@array_keys($t[all]);
//    for ($i=0; $i<count($k); $i++) 
//    {
//       $id=$k[$i];        
//       $sum=(double)$t[$id][cena]*(double)$t[$id][kol];
//       $sum2+=$sum;
//    }

//    $timeT=getdate();
//    $dt=$timeT[year].".".$timeT[mon].".".$timeT[mday];
//    $tm=$timeT[hours].":".$timeT[minutes].":".$timeT[seconds];  

//    $usertable = "skid";
//    $query="select * from $usertable";
//    $r=mysql_query($query);
//    $kl=mysql_num_rows($r);
//    for ($i=0; $i<$kl; $i++)
//    {
//       $svishe=convert_cyr_string(mysql_result($r,$i,1),"d","w");
//       $sk=convert_cyr_string(mysql_result($r,$i,2),"d","w");
//       if ($sum2>$svishe) $skid=$sk;
//    }

//    $S2=(double)$sum2/100*(double)$skid;
//    $S2=$sum2-$S2;

//    $dtd.=$Dv[12].".".$Dv[11].".".$Dv[10];
//    $tmot.=$Dv[13].":".$Dv[14].":00";
//    $tmdo.=$Dv[15].":".$Dv[16].":00";

//    $usertable = "zakaz";
//    /* Вставить информацию о заказе в таблицу zakaz*/
//    $query="INSERT INTO $usertable set id_pok='$num',".
//           "date='$dt', time='$tm', itogo='$sum2', itogo_skid='$S2',".
//           "adress_dost='$Dv[9]', date_dost='$dtd', time_ot='$tmot',".
//           " time_do='$tmdo', dost='N'";
//    $result = mysql_query($query);

//    $q="select id from $usertable";
//    $r=mysql_query($q);
//    $n2=mysql_num_rows($r);
//    $num2=convert_cyr_string(mysql_result($r,$n2-1,0),"d","w");


//    $usertable = "dopoln";

//    $k=@array_keys($t[all]);
//    for ($i=0; $i<count($k); $i++) 
//    {
//       $id=$k[$i];        
//       $sum=(double)$t[$id][cena]*(double)$t[$id][kol];
//       $sum2+=$sum;
//       $cen_sk=$t[$id][cena]/100*$skid;
//       $cen_sk=$t[$id][cena]-$cen_sk;
//       $sum_sk=$cen_sk*$t[$id][kol];
//       $it_sk+=$sum_sk;
//       $kol=$t[$id][kol];
//       /* Вставить информацию в таблицу dopoln*/
//       $query="INSERT INTO $usertable set id_zak='$num2',".
//              "id_tov='$id',kol_tov='$kol', summa='$sum', skid='$skid'";
//       $result = mysql_query($query);
//    }   
//    $usertable = "zakaz";
//    /* Вставить информацию о заказе в таблицу zakaz*/
//    $query="INSERT INTO $usertable set itogo_skid='$it_sk' where id='$num2'";
//    $result = mysql_query($query);

//    $usertable = "tovar";

//    $k=@array_keys($t[all]);
//    for ($i=0; $i<count($k); $i++) 
//    {
//       $id=$k[$i];        
//       $kol=$t[$id][kol];

//       $query="select kol_vo from $usertable where id=$id";
//       $result=mysql_query($query);
//       $kolich=mysql_result($result,0,0);
//       $kolich-=$kol;  
//       $query="update $usertable set kol_vo='$kolich', rezerv='$kol' where id=$id";   
//       $result=mysql_query($query);
//    }

//    unset($Dv);
//    mysql_close();
// }
// //-------------



// //-------------
// function view($n)
// {
//    global $t, $hostname, $username, $password, $dbName, $usertable, $coltab;
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");
//    $query="select info, pict from $usertable";
//    $f=mysql_query($query);  
//    $in=convert_cyr_string(mysql_result($f,$n,0),"d","w");
//    $pict=mysql_result($f,$n,1);
//    echo "<center><table border=2><tr><td bgcolor=$coltab>$in</td></tr></table></center>".
//         "<br><center><table border=0><tr><td><IMG src=$pict></td></tr></table></center><br>"; 
//    mysql_close();
// }
// //-------------





// //--------------------------------

// // ФУНКЦИИ АДМИНИСТРАЦИИ

// //--------------------------------

// //-------------
// function admin() 
// {
//    global $hostname, $enter, $t, $SID, $login, $pass;

//    $enter="нет";
 
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "mysql";
//    $usertable = "user"; 

//    /* создать соединение */
//    $conect=@mysql_connect($hostname,$login,$pass);
//    if ($conect!="false") 
//    { 
//       //выбираем БД
//       @mysql_select_db("$dbName");
//       $query="select * from $usertable";
//       //делаем запрос в mysql
//       $f=@mysql_query($query);
//       $y=@mysql_num_rows($f); 
//       unset($enter);   
   
//       for ($i=0; $i<$y; $i++) 
//       {     
//          $a[]=convert_cyr_string(@mysql_result($f,$i,1),"d","w");
//          $a[]=convert_cyr_string(@mysql_result($f,$i,6),"d","w");
//          if (($a[0]==$login)&($a[1]=="Y")) 
//          {
//             $enter="да";    
//             break;
//          }
//          unset($a);
//       }  
//       @mysql_close();

//       if ($enter=="да")
//       {
//          //exit(header("Location: ?c=nazad&SID=$SID"));
         ?>   
            <!-- <center><table border=2><td BGCOLOR=white>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <a href= ?c=tov&SID=$SID>Данные о товаре</a>&nbsp;&nbsp;&nbsp;&nbsp;
              &nbsp;&nbsp;&nbsp;</td><td BGCOLOR=white>&nbsp;&nbsp;&nbsp;
              &nbsp;&nbsp;&nbsp;
              <a href= ?c=zakaz&SID=$SID>Данные о заказах</a>&nbsp;
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td BGCOLOR=white>
              &nbsp;&nbsp;&nbsp;
              <a href= ?c=infop&SID=$SID>Данные о покупателях</a>&nbsp;
              &nbsp;&nbsp;</td><td BGCOLOR=white>
              &nbsp;&nbsp;&nbsp;
              <a href= ?c=infoskid&SID=$SID>Данные о скидках</a>&nbsp;
              &nbsp;&nbsp;</td></table></center><br><br>

              <center><table border=2><td BGCOLOR=white>&nbsp;
              <a href= ?c=dob&SID=$SID>Добавление данных в БД</a>&nbsp;</td>
              <td BGCOLOR=white>&nbsp;<a href= ?c=uvel&SID=$SID>Увеличение кол-ва товара
              </a>&nbsp;</td></table></center> -->
         <?php         
//       }else echo "<br><br><br><br><center><h2>Введен неверный логин или пароль!!!</h2></center>";


//    }else echo "<br><br><br><br><center><h2>Введен неверный логин или пароль!!!</h2></center>";

// }
// //-------------


// //-------------
// function infotov()
// {
//    global $ogl_tov, $t, $SID, $hostname, $coltab;
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "shop";
//    $usertable = "tovar"; 
 
//    /* создать соединение */
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    //выбираем БД
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");
//    $query="select * from $usertable";
//    //делаем запрос в mysql
//    $f=mysql_query($query);

//    $x=count($ogl_tov);          // вычисляем число колонок
//    $y=mysql_num_rows($f);   // и число строк
//    echo "<center><table border=2><tr>";
//    // рисуем заголовок таблицы   
//    for ($j=0; $j<$x; $j++) 
//    {
//       if (strlen($ogl_tov[$j])==0) echo "<th BGCOLOR=$coltab>&nbsp;</th>";
//       else echo "<th BGCOLOR=$coltab>$ogl_tov[$j]</th>";
//    }
// echo "<th BGCOLOR=$coltab>&nbsp;</th>";
//    echo "</tr>";
//    // основной цикл вывода данных о покупателях
//    for ($i=0; $i<$y; $i++) 
//    {     
//      $a[]=$i+1;
//      $a[]=convert_cyr_string(mysql_result($f,$i,1),"d","w");
//      $a[]=convert_cyr_string(mysql_result($f,$i,2),"d","w");
//      $a[]=convert_cyr_string(mysql_result($f,$i,3),"d","w");
//      $a[]=convert_cyr_string(mysql_result($f,$i,4),"d","w");
//      $a[]=convert_cyr_string(mysql_result($f,$i,5),"d","w");
//      $a[]=convert_cyr_string(mysql_result($f,$i,6),"d","w");
//      $a[]=convert_cyr_string(mysql_result($f,$i,7),"d","w"); 
//      $a[]=convert_cyr_string(mysql_result($f,$i,9),"d","w");           
//      echo "<tr ALIGN=center>";
//      // цикл вывода всех колонок текущей строки таблицы
//      for ($j=0; $j<$x-1; $j++) 
//      {
//          // если ячейка пустая, там надо поместить "&nbsp;";
//          if (strlen($a[$j])==0) echo "<td BGCOLOR=$coltab>&nbsp;</td>";
//          else echo "<td BGCOLOR=$coltab>$a[$j]</td>";
//      }     

// $id=mysql_result($f,$i,0);
// echo "<td BGCOLOR=$coltab><a href=$PHP_SELF?c=asort&id=$id&SID=$SID>$a[$j]</a></td>";
// echo "<td BGCOLOR=$coltab><a href=$PHP_SELF?c=del&id=$id&SID=$SID>удалить</a></td>";
//      echo "</tr>";
//      unset($a);
//    }
//    echo "</table></center>";
//    mysql_close();
// }
// //-------------



// //-------------
// function zakaz()
// {
//    global $ogl_zak, $t, $SID, $hostname, $coltab;
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "shop";
//    $usertable = "zakaz"; 
 
//    /* создать соединение */
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    //выбираем БД
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");
//    $query="select * from $usertable";

//    //делаем запрос в mysql
//    $f=mysql_query($query);

//    $x=count($ogl_zak);      // вычисляем число колонок
//    $y=mysql_num_rows($f);   // и число строк

//    echo "<center><table border=2><tr>";
//    // рисуем заголовок таблицы   
//    for ($j=0; $j<$x; $j++) 
//    {
//       if (strlen($ogl_zak[$j])==0) echo "<th BGCOLOR=$coltab>&nbsp;</th>";
//       else echo "<th BGCOLOR=$coltab>$ogl_zak[$j]</th>";
//    }
//    echo "</tr>";

//    // основной цикл вывода данных о заказах
//    for ($i=0; $i<$y; $i++) 
//    {      
//       $a[]=convert_cyr_string(mysql_result($f,$i,0),"d","w");     
//       $a[]=convert_cyr_string(mysql_result($f,$i,1),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,2),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,3),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,4),"d","w");     
//       $a[]=convert_cyr_string(mysql_result($f,$i,5),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,10),"d","w");

//       $query="select naim_org, fam from pokup where id=$a[1]";
//       $f2=mysql_query($query);

//       $b[]=convert_cyr_string(mysql_result($f2,0,0),"d","w");
//       $b[]=convert_cyr_string(mysql_result($f2,0,1),"d","w");     

//       $query="select id_tov, kol_tov, summa, skid from dopoln where id_zak=$a[0]";
//       $f3=mysql_query($query);
//       $y3=mysql_num_rows($f3);     
     
//       // цикл вывода всех колонок текущей строки таблицы
            
//       for ($k=0; $k<$y3; $k++)
//       {
//          echo "<tr ALIGN=center>";
//          echo "<td BGCOLOR=$coltab>$a[0]</td>";
//          if ($k==0) 
//          {
              
//             echo "<td BGCOLOR=$coltab>$a[1]</td>".
//                  "<td BGCOLOR=$coltab>$b[0]</td>".
//                  "<td BGCOLOR=$coltab>$b[1]</td>";                  
//          }
//          else
//          {
//             echo "<td BGCOLOR=$coltab>-</td>".                    
//                  "<td BGCOLOR=$coltab>-</td>".
//                  "<td BGCOLOR=$coltab>-</td>";
//          }

//          $c[]=convert_cyr_string(mysql_result($f3,$k,0),"d","w");
//          $c[]=convert_cyr_string(mysql_result($f3,$k,1),"d","w"); 
//          $c[]=convert_cyr_string(mysql_result($f3,$k,2),"d","w");                    
//          $c[]=convert_cyr_string(mysql_result($f3,$k,3),"d","w");                    
 
//          $query="select naim, cena from tovar where id=$c[0]";
//          $f4=mysql_query($query);
//          $d[]=convert_cyr_string(mysql_result($f4,0,0),"d","w");
//          $d[]=convert_cyr_string(mysql_result($f4,0,1),"d","w");
  
//          echo "<td BGCOLOR=$coltab>$d[0]</td>".
//               "<td BGCOLOR=$coltab>$d[1]</td>".              
//               "<td BGCOLOR=$coltab>$c[1]</td>".              
//               "<td BGCOLOR=$coltab>$c[2]</td>";
 
//          if ($k==0) 
//          {
//             echo "<td BGCOLOR=$coltab>$a[4]</td>".
//                  "<td BGCOLOR=$coltab>$c[3]</td>".              
//                  "<td BGCOLOR=$coltab>$a[5]</td>".
//                  "<td BGCOLOR=$coltab>$a[2]</td>".
//                  "<td BGCOLOR=$coltab>$a[3]</td>".
//                  "<td BGCOLOR=$coltab>$a[6]</td>";
//          }
//          else
//          {
//             echo "<td BGCOLOR=$coltab>-</td>".
//                  "<td BGCOLOR=$coltab>-</td>".
//                  "<td BGCOLOR=$coltab>-</td>".
//                  "<td BGCOLOR=$coltab>-</td>".
//                  "<td BGCOLOR=$coltab>-</td>".
//                  "<td BGCOLOR=$coltab>&nbsp;</td>";
//          }
//          echo "</tr>";
//          unset($c);
//          unset($d);
//       }             
     
//       unset($a);
//       unset($b);
     
//    }
//    echo "</table></center>";
//    mysql_close();
// }
// //-------------



// //-------------
// function infop()
// {
//    global $ogl, $t, $SID, $hostname, $coltab;
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "shop";
//    $usertable = "pokup"; 
 
//    /* создать соединение */
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    //выбираем БД
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");
//    $query="select * from $usertable";
//    //делаем запрос в mysql
//    $f=mysql_query($query);

//    $x=count($ogl);          // вычисляем число колонок
//    $y=mysql_num_rows($f);   // и число строк
//    echo "<center><table border=2><tr>";
//    // рисуем заголовок таблицы   
//    for ($j=0; $j<$x; $j++) 
//    {
//       if (strlen($ogl[$j])==0) echo "<th BGCOLOR=$coltab>&nbsp;</th>";
//       else echo "<th BGCOLOR=$coltab>$ogl[$j]</th>";
//    }
//    echo "</tr>";
//    // основной цикл вывода данных о покупателях
//    for ($i=0; $i<$y; $i++) 
//    {     
//       $a[]=$i+1;
//       $a[]=convert_cyr_string(mysql_result($f,$i,1),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,2),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,3),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,4),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,5),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,6),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,7),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,8),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,9),"d","w");
//       if (count($a)<2) continue; // если она пустая (глюки), пропускаем
//       echo "<tr ALIGN=center>";
//       // цикл вывода всех колонок текущей строки таблицы
//       for ($j=0; $j<$x; $j++) 
//       {
//          // если ячейка пустая, там надо поместить "&nbsp;";
//          if (strlen($a[$j])==0) echo "<td BGCOLOR=$coltab>&nbsp;</td>";
//          else echo "<td BGCOLOR=$coltab>$a[$j]</td>";
//       }     
//       echo "</tr>";
//       unset($a);
//    }
//    echo "</table></center>";
//    mysql_close();
// }
// //-------------



// //-------------
// function dob()
// {
//    global $ogl_t2, $t, $SID, $hostname, $coltab, $v;
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "shop";
//    $usertable = "tovar"; 
 
//    /* создать соединение */
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    //выбираем БД
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");

//    for ($i=0; $i<10; $i++)
//    { 
//       $v2[]=convert_cyr_string($v[$i],"w","d");
//    }

//    $query="insert into $usertable set naim='$v2[0]', articul='$v2[1]', prihod='$v2[2]', kol_vo='$v2[3]', rezerv='$v2[4]', nacenka='$v2[5]', cena='$v2[6]', info='$v2[7]', asort='$v2[8]', pict='$v2[9]'";
//    //делаем запрос в mysql
//    $f=mysql_query($query); 

//    mysql_close();
// }
// //-------------



// //-------------
// function delete($id)
// {
//    global $t, $hostname;
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "shop";
//    $usertable = "tovar"; 

//  /* создать соединение */
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    //выбираем БД
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");

//    $query="delete from $usertable where id=$id";
//    $f=mysql_query($query);
//    mysql_close();
// }
// //-------------




// //-------------
// function asortiment($id)
// {
//    global $t, $hostname;
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "shop";
//    $usertable = "tovar"; 

//  /* создать соединение */
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    //выбираем БД
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");

//    $query="select asort from $usertable where id=$id";
//    $f=mysql_query($query);
//    $as=convert_cyr_string(mysql_result($f,0,0),"d","w");
//    if ($as=='Y') $as='N';
//    else $as='Y';
//    $query="update $usertable set asort='$as' where id=$id";   
//    $f=mysql_query($query);
//    mysql_close();
// }
// //-------------



// //-------------
// function sure($s)
// {   
//    global $id;
//    echo "<form action=$PHP_SELF?c=$s&SID=$SID&id=$id method=POST>".
//         "<br><br><br><br><br><br><br><br>".
//         "<h2><center><font color=red><b>ВЫ УВЕРЕНЫ?</b></font></center></h2>".
//         "<center><input type=submit name=da value='ДА'> &nbsp;".
//         "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
//         "<input type=submit name=net value='НЕТ'></center></form>";
// }
// //-------------



// //-------------
// function uvel()
// {   
//    global $ogl_tov, $t, $SID, $hostname, $coltab, $id;
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "shop";
//    $usertable = "tovar"; 
 
//    /* создать соединение */
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    //выбираем БД
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");
//    $query="select * from $usertable";
//    //делаем запрос в mysql
//    $f=mysql_query($query);

//    $y=mysql_num_rows($f);   // и число строк

//    echo "<form action=$PHP_SELF?c=izm&SID=$SID&v=$v method=POST>";
//    echo "<center><table border=2><tr>";
//    // рисуем заголовок таблицы   
   
//    echo "<th BGCOLOR=$coltab>$ogl_tov[0]</th>";
//    echo "<th BGCOLOR=$coltab>$ogl_tov[1]</th>";
//    echo "<th BGCOLOR=$coltab>$ogl_tov[2]</th>";
//    echo "<th BGCOLOR=$coltab>$ogl_tov[4]</th>";
//    echo "<th BGCOLOR=$coltab>&nbsp;</th>";
//    echo "<th BGCOLOR=$coltab>$ogl_tov[4]</th>";
//    //echo "<th BGCOLOR=$coltab>&nbsp;</th>";
//    echo "</tr>";

//    // основной цикл вывода данных о покупателях
//    for ($i=0; $i<$y; $i++) 
//    {     
//      $a[]=$i+1;
//      $a[]=convert_cyr_string(mysql_result($f,$i,1),"d","w");
//      $a[]=convert_cyr_string(mysql_result($f,$i,2),"d","w");
//      $a[]=convert_cyr_string(mysql_result($f,$i,4),"d","w");
           
//      echo "<tr ALIGN=center>";
//      // цикл вывода всех колонок текущей строки таблицы
//      for ($j=0; $j<count($a); $j++) 
//      {
//          // если ячейка пустая, там надо поместить "&nbsp;";
//          if (strlen($a[$j])==0) echo "<td BGCOLOR=$coltab>&nbsp;</td>";
//          else echo "<td BGCOLOR=$coltab>$a[$j]</td>";
//      }     

//      $id=mysql_result($f,$i,0);

//      echo "<td BGCOLOR=$coltab>+</td>".
//           "<td BGCOLOR=$coltab>".
//           "<input size=4 type=text name=v[$id]></td></tr>";
//      unset($a);
//    }
//    echo "</table><input type=submit value='Добавить товар'></center></form>";
//    mysql_close();
// }
// //-------------



// //------------- Изменение количества товара
// function izmkol()
// {
//    global $t, $hostname, $v;
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "shop";
//    $usertable = "tovar"; 

//  /* создать соединение */
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    //выбираем БД
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");

// $k=@array_keys($v);
// for ($i=0; $i<count($k); $i++) 
// {
//          $id=$k[$i];


//    $query="select kol_vo from $usertable where id='$id'";
//    $f=mysql_query($query);

//    $kol=mysql_result($f,0,0);
//    if ($v[$id]=='') $v[$id]=0;
//    $kol+=$v[$id];

//    $query="update $usertable set kol_vo='$kol' where id='$id'";   
//    $f=mysql_query($query);
// }
//    mysql_close();
// }
// //-------------




// //------------- Информация о скидках
// function insk()
// {
//    global $t, $hostname, $ogl_sk, $coltab;
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "shop";
//    $usertable = "skid"; 

//  /* создать соединение */
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    //выбираем БД
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");

//    $query="select * from $usertable";
//    $f=mysql_query($query);

//    echo "<center><table border=2><tr>";
//    for ($j=0; $j<count($ogl_sk); $j++) 
//    {
//       // если ячейка пустая, там надо поместить "&nbsp;";
//       if (strlen($ogl_sk[$j])==0) echo "<th BGCOLOR=$coltab>&nbsp;</th>";
//       echo "<th BGCOLOR=$coltab>$ogl_sk[$j]</th>";
//    }
//    echo "<th BGCOLOR=$coltab>&nbsp;</th>";
//    $y=mysql_num_rows($f);   

//    for ($i=0; $i<$y; $i++) 
//    {     
//       $a[]=$i+1;
//       $a[]=convert_cyr_string(mysql_result($f,$i,1),"d","w");
//       $a[]=convert_cyr_string(mysql_result($f,$i,2),"d","w");
     
//       if (count($a)<2) continue; // если она пустая (глюки), пропускаем
//       echo "<tr ALIGN=center>";
//       // цикл вывода всех колонок текущей строки таблицы
//       for ($j=0; $j<count($ogl_sk); $j++) 
//       {
//          // если ячейка пустая, там надо поместить "&nbsp;";
//          if (strlen($a[$j])==0) echo "<td BGCOLOR=$coltab>&nbsp;</td>";
//          else echo "<td BGCOLOR=$coltab>$a[$j]</td>";
//       }  
   
//       $id=mysql_result($f,$i,0);
//       echo "<td BGCOLOR=$coltab><a href=$PHP_SELF?c=delsk&id=$id&SID=$SID>удалить</a></td>";
//       echo "</tr>";
//       unset($a);
//    }
//    echo "</table></center><br>";


//    echo "<form action=$PHP_SELF?c=dobvsk&SID=$SID&v1=$v1&v2=$v2 method=POST>".
//         "<center><table border=2><tr>".
//         "<th BGCOLOR=$coltab>Более</th>".
//         "<td><input type=text size=7 name='v1'></td>".
//         "<th BGCOLOR=$coltab>Скидка</th>".
//         "<td><input type=text size=7 name='v2'></td>".
//         "<td><input type=submit value='Добавить'></td>".
//         "</tr></table></center></form>"; 
 
//    mysql_close(); 
// }
// //-------------



// // Удаление строки из таблицы skid
// //-------------
// function dlsk($id)
// {
//    global $t, $hostname;
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "shop";
//    $usertable = "skid"; 

//    /* создать соединение */
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    //выбираем БД
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");

//    $query="delete from $usertable where id=$id";
//    $f=mysql_query($query);
//    mysql_close();
// }
// //-------------




// // Добавление строки в таблицу skid       
// //-------------
// function dbsk($v1,$v2)
// {
//    global $t, $hostname;
//    $username = $t[0][log];
//    $password = $t[0][pas];
//    $dbName = "shop";
//    $usertable = "skid"; 

//    /* создать соединение */
//    mysql_connect($hostname,$username,$password) or die ("Нет соединения с MySQL");
//    //выбираем БД
//    mysql_select_db("$dbName")or die("Не могу выбрать базу данных ");

//    $query="insert into $usertable set svishe='$v1', skid='$v2'";
//    $f=mysql_query($query);
//    mysql_close();
// }
// //-------------

?>