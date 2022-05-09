
const fs = require('fs')

const server = require('./my')
require('./my/dotenv')

const PORT = process.env.PORT || 3465

const app = server()

app.get('/', (req, res) => {
	//res.setHeader('Content-Type', 'application/json')
	res.writeHead(200, {'Content-Type': 'text/plain; charset=utf-8'})
	res.end('Добро пожаловать!\n\n' + 'Server run on http://localhost:' + PORT)
})

app.get('/random', (req, res) => {
	if (req.params) res.end('params ' + JSON.stringify(req.params))
	else res.end('cooool (:')
})

app.get('/home', (req, res) => {
	fs.readFile(__dirname + "/static/pages/home/index.html", (err, html) => {
		res.end(html)
	})
})

app.listen(PORT, () => {
  console.log(`Server run on http://localhost:${PORT}`)
})