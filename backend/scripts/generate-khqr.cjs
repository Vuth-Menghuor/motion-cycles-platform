const { BakongKHQR, khqrData, IndividualInfo } = require('bakong-khqr')

const readInput = () =>
  new Promise((resolve, reject) => {
    let input = ''
    process.stdin.setEncoding('utf8')
    process.stdin.on('data', (chunk) => (input += chunk))
    process.stdin.on('end', () => {
      try {
        resolve(JSON.parse(input))
      } catch {
        reject(new Error('Invalid KHQR generation input'))
      }
    })
    process.stdin.on('error', reject)
  })

const fail = (message) => {
  process.stdout.write(JSON.stringify({ success: false, message }))
  process.exitCode = 1
}

readInput()
  .then((input) => {
    const expirationMinutes = Number(input.expiration_minutes || 15)
    if (!Number.isFinite(expirationMinutes) || expirationMinutes <= 0 || expirationMinutes > 60) {
      throw new Error('KHQR expiration must be between 1 and 60 minutes')
    }

    const info = new IndividualInfo(input.bakong_account, input.account_name, 'PHNOM PENH', {
      currency: input.currency === 'USD' ? khqrData.currency.usd : khqrData.currency.khr,
      amount: Number(input.amount),
      merchantCategoryCode: '5999',
      expirationTimestamp: Date.now() + expirationMinutes * 60 * 1000,
    })
    const response = new BakongKHQR().generateIndividual(info)

    if (response.status?.code !== 0 || !response.data?.qr) {
      throw new Error(response.status?.message || 'Official KHQR SDK could not generate a QR code')
    }

    process.stdout.write(JSON.stringify({
      success: true,
      qr: response.data.qr,
      md5: response.data.md5,
      expires_at: new Date(info.expirationTimestamp).toISOString(),
    }))
  })
  .catch((error) => fail(error.message || 'Official KHQR SDK failed'))
