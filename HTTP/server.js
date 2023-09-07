
const http = require("http")

const host = 'localhost'
const port = 80;

const requestListener = function (req, res) {
    res.end('good')
}

const server = http.createServer(requestListener)

server.listen(port, host, () => {
    console.log(`Server is running on http://${host}:${port}`)
})
