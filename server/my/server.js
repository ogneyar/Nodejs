
const http = require('http')
const fs = require('fs')
const path = require('path')


const Server = class {
	
	constructor() {
		this.run()
	}
	server = null
	router = []
	
	get(route, func) {
		this.router.push({route,func})
	}
	
    run() { 
    	this.server = http.createServer((req, res) => {
	    	let url = req.url.split('?')[0]
			req.params = this.parseParams(req)

			if (url.split('.')[1] !== undefined) {
				if (url.split('.')[1] === "css") {
					fs.readFile(path.join(__dirname, '..', url), (err, css) => {
						res.writeHead(200, {'Content-Type': 'text/css; charset=utf-8'})
						res.end(css.toString())
					})
					return null
				}
				if (url.split('.')[1] === "js") {
					fs.readFile(path.join(__dirname, '..', url), (err, js) => {
						res.writeHead(200, {'Content-Type': 'application/javascript; charset=utf-8'})
						res.end(js.toString())
					})
					return null
				}
			}
	    	
	    	let no = true
	    	
	    	this.router.forEach(i => {
	    		if (i.route === url) {
	    			no = false
	    			i.func(req, res)
	    		}
	    	})
	    	
	    	if (no) {
	    		res.writeHead(404, {'Content-Type': 'text/html; charset=utf-8'})
	    		res.end('Error 404 Page Not Found')
	    	}
		})
    }
    
    listen(...args) { 
    	if (this.server) this.server.listen(...args)
    }

	parseParams(req) {
		return req.url.split('?')[1] 	// если в запросе есть знак вопроса
			&&							// то
				JSON.parse(				// переводим строку в объект
					"{" + req.url
						.split('?')[1]	// берём значение после знака вопроса
						.split("&") 	// делим по знаку амперсанта
						// в в цикле переводим строку из "temp=true&test=false" в массив [`"temp":"true"`,`"test":"false"`]
						.map(i => `"${i.split("=")[0]}":"${i.split("=")[1]}"`) 
						.join(',') 		// переводим массив в строку, объединяя запятой - `"temp":"true","test":"false"`
					+ "}"
				) 
			|| undefined 				// иначе возвращаем undefined

	}
}


module.exports = () => new Server()