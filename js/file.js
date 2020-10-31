var fs = require('fs');

fs.mkdir('temp', function() {
    fs.writeFile('./temp/temp.txt', 'Hell Work', function() {
        console.log('Всё работает');
        console.log('Создал папку и файл');
    });
});
