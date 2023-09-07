
const axios = require('axios')
const UDP = require('dgram')

const server = UDP.createSocket('udp4')

const port = 2222

server.on('listening', () => {
	// Server address it’s using to listen
	const address = server.address()

	console.log('Listining to ', 'Address: ', address.address, 'Port: ', address.port)
})

server.on('message', async (message, info) => {
	console.log('Принятое сообщение: \x1b[32m', message.toString(), '\x1b[0m')

	let response = Buffer.from('Сообщение принято!')

	if (message.toString() === "get_echo") {
		try{
			await axios.get('https://server.leidtogi.ru/echo')
				.then(data => {
					console.log(data.data)
					response = Buffer.from(JSON.stringify(data.data))
				})
				.catch(error => {
					console.error(error)
				})
		}catch(e) {
			console.log('\x1b[32mОшибка запроса к LeidTogi!\x1b[0m')
			response = Buffer.from('Ошибка запроса к LeidTogi!')
		}
	}

	//sending back response to client
	server.send(response, info.port, info.address, (err) => {
		if (err) {
			console.error('Failed to send response !!')
		} else {
			console.log('Принял пакет, замечательно!')
		}
	})
})

server.bind(port)
