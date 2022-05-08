
const fs = require('fs')
const path = require('path')

const dotenv = () => {
	try {
		let data = fs.readFileSync(path.resolve(__dirname, '..', '.env'))
		
		data.toString()
			.trim()
			.replace('\r', '')
			.split('\n')
			.forEach(i => {
				let row = i.split('=')
				process.env[`${row[0]}`] = row[1]
			})
	}catch(err) { 
		process.env.ERROR = err
	}
	
}


module.exports = dotenv()