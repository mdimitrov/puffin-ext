function httpRequest(data) {
    const requestObj = { method: data.method, credentials: 'same-origin' };

    if (requestObj.method === 'POST') {
        requestObj.body = JSON.stringify(data.body);
        requestObj.headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    }
    return fetch(data.url, requestObj)
        .then(function(response) {
            return response.json()
        })
}