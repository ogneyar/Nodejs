
const UDP = require('dgram')

const client = UDP.createSocket('udp4')

const port = 2222

const hostname = 'localhost'

client.on('message', (message, info) => {
	// get the information about server address, port, and size of packet received.

	console.log('Address: ', info.address, 'Port: ', info.port, 'Size: ', info.size)

	//read message from server

	console.log('Сообщение от сервера: \x1b[32m', message.toString(), '\x1b[0m')
})

// const packet = Buffer.from('Это сообщение отправил клиент!')
const packet = Buffer.from('get_echo')

client.send(packet, port, hostname, (err) => {
	if (err) {
		console.error('Failed to send packet !!')
	} else {
		console.log('Передан пакет!')
	}
})

