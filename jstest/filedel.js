var fs = require('fs');

fs.unlink('./temp/temp.txt', function() {    
    fs.rmdir('temp', function() {
        console.log('Всё работает');
        console.log('Удалил папку и файл');
    });
});