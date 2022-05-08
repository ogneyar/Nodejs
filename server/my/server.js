
const http = require('http')

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
}


module.exports = () => new Server()