<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
<tr><th align='left' bgcolor='#f57900' colspan=\"5\"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> ErrorException: mb_strpos() expects parameter 1 to be string, object given in /home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Support/Str.php on line <i>103</i></th></tr>\n<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>\n<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>\n<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0006</td><td bgcolor='#eeeeec' align='right'>357248</td><td bgcolor='#eeeeec'>{main}( )</td><td title='/home/vagrant/www/photogram_API/public/index.php' bgcolor='#eeeeec'>.../index.php<b>:</b>0</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>2</td><td bgcolor='#eeeeec' align='center'>0.1096</td><td bgcolor='#eeeeec' align='right'>772816</td><td bgcolor='#eeeeec'>App\\Http\\Kernel->handle( )</td><td title='/home/vagrant/www/photogram_API/public/index.php' bgcolor='#eeeeec'>.../index.php<b>:</b>53</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>3</td><td bgcolor='#eeeeec' align='center'>0.1096</td><td bgcolor='#eeeeec' align='right'>772816</td><td bgcolor='#eeeeec'>App\\Http\\Kernel->sendRequestThroughRouter( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php' bgcolor='#eeeeec'>.../Kernel.php<b>:</b>116</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>4</td><td bgcolor='#eeeeec' align='center'>0.3766</td><td bgcolor='#eeeeec' align='right'>1761680</td><td bgcolor='#eeeeec'>Illuminate\\Routing\\Pipeline->then( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php' bgcolor='#eeeeec'>.../Kernel.php<b>:</b>151</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>5</td><td bgcolor='#eeeeec' align='center'>0.3766</td><td bgcolor='#eeeeec' align='right'>1765856</td><td bgcolor='#eeeeec'>Illuminate\\Routing\\Pipeline->Illuminate\\Routing\\{closure}( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php' bgcolor='#eeeeec'>.../Pipeline.php<b>:</b>102</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>6</td><td bgcolor='#eeeeec' align='center'>0.3766</td><td bgcolor='#eeeeec' align='right'>1766872</td><td bgcolor='#eeeeec'>Illuminate\\Routing\\Pipeline->Illuminate\\Pipeline\\{closure}( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Routing/Pipeline.php' bgcolor='#eeeeec'>.../Pipeline.php<b>:</b>53</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>7</td><td bgcolor='#eeeeec' align='center'>0.3767</td><td bgcolor='#eeeeec' align='right'>1767248</td><td bgcolor='#eeeeec'>Dingo\\Api\\Http\\Middleware\\Request->handle( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php' bgcolor='#eeeeec'>.../Pipeline.php<b>:</b>149</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>8</td><td bgcolor='#eeeeec' align='center'>0.3842</td><td bgcolor='#eeeeec' align='right'>1782336</td><td bgcolor='#eeeeec'>Dingo\\Api\\Http\\Middleware\\Request->sendRequestThroughRouter( )</td><td title='/home/vagrant/www/photogram_API/vendor/dingo/api/src/Http/Middleware/Request.php' bgcolor='#eeeeec'>.../Request.php<b>:</b>103</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>9</td><td bgcolor='#eeeeec' align='center'>0.3843</td><td bgcolor='#eeeeec' align='right'>1783144</td><td bgcolor='#eeeeec'>Illuminate\\Pipeline\\Pipeline->then( )</td><td title='/home/vagrant/www/photogram_API/vendor/dingo/api/src/Http/Middleware/Request.php' bgcolor='#eeeeec'>.../Request.php<b>:</b>127</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>10</td><td bgcolor='#eeeeec' align='center'>0.3843</td><td bgcolor='#eeeeec' align='right'>1786624</td><td bgcolor='#eeeeec'>Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php' bgcolor='#eeeeec'>.../Pipeline.php<b>:</b>102</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>11</td><td bgcolor='#eeeeec' align='center'>0.3862</td><td bgcolor='#eeeeec' align='right'>1787712</td><td bgcolor='#eeeeec'>Illuminate\\Foundation\\Http\\Middleware\\CheckForMaintenanceMode->handle( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php' bgcolor='#eeeeec'>.../Pipeline.php<b>:</b>149</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>12</td><td bgcolor='#eeeeec' align='center'>0.3864</td><td bgcolor='#eeeeec' align='right'>1787712</td><td bgcolor='#eeeeec'>Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/CheckForMaintenanceMode.php' bgcolor='#eeeeec'>.../CheckForMaintenanceMode.php<b>:</b>46</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>13</td><td bgcolor='#eeeeec' align='center'>0.3879</td><td bgcolor='#eeeeec' align='right'>1790248</td><td bgcolor='#eeeeec'>Barryvdh\\Cors\\HandleCors->handle( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php' bgcolor='#eeeeec'>.../Pipeline.php<b>:</b>149</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>14</td><td bgcolor='#eeeeec' align='center'>0.3879</td><td bgcolor='#eeeeec' align='right'>1790248</td><td bgcolor='#eeeeec'>Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}( )</td><td title='/home/vagrant/www/photogram_API/vendor/barryvdh/laravel-cors/src/HandleCors.php' bgcolor='#eeeeec'>.../HandleCors.php<b>:</b>37</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>15</td><td bgcolor='#eeeeec' align='center'>0.3895</td><td bgcolor='#eeeeec' align='right'>1790984</td><td bgcolor='#eeeeec'>App\\Http\\Middleware\\ProfileJsonResponse->handle( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php' bgcolor='#eeeeec'>.../Pipeline.php<b>:</b>149</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>16</td><td bgcolor='#eeeeec' align='center'>0.3895</td><td bgcolor='#eeeeec' align='right'>1790984</td><td bgcolor='#eeeeec'>Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}( )</td><td title='/home/vagrant/www/photogram_API/app/Http/Middleware/ProfileJsonResponse.php' bgcolor='#eeeeec'>.../ProfileJsonResponse.php<b>:</b>12</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>17</td><td bgcolor='#eeeeec' align='center'>0.3985</td><td bgcolor='#eeeeec' align='right'>1867088</td><td bgcolor='#eeeeec'>Barryvdh\\Debugbar\\Middleware\\InjectDebugbar->handle( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php' bgcolor='#eeeeec'>.../Pipeline.php<b>:</b>149</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>18</td><td bgcolor='#eeeeec' align='center'>0.5416</td><td bgcolor='#eeeeec' align='right'>2475888</td><td bgcolor='#eeeeec'>Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}( )</td><td title='/home/vagrant/www/photogram_API/vendor/barryvdh/laravel-debugbar/src/Middleware/InjectDebugbar.php' bgcolor='#eeeeec'>.../InjectDebugbar.php<b>:</b>65</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>19</td><td bgcolor='#eeeeec' align='center'>0.5416</td><td bgcolor='#eeeeec' align='right'>2475888</td><td bgcolor='#eeeeec'>Dingo\\Api\\Http\\Middleware\\Request->Dingo\\Api\\Http\\Middleware\\{closure}( )</td><td title='/home/vagrant/www/photogram_API/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php' bgcolor='#eeeeec'>.../Pipeline.php<b>:</b>114</td></tr>\n<tr><td bgcolor='#eeeeec' align='center'>20</td><td bgcolor='#eeeeec' align='center'>0.5416</td><td bgcolor='#eeeeec' align='right'>2475888</td><td bgcolor='#eeeeec'>Dingo\\Api\\Routing\\Router->dispatch( )</td><td title='/home/vagrant/www/photogram_API/vendor/dingo/api/src/Http/Middleware/Request.php' bgcolor='#eeeeec'>.../Request.php<b>:</b>126</td></tr>
</body>
</html>