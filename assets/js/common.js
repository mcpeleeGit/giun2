function fetchApi(url, options = {}) {
    return fetch(url, options)
            .then(response => response.json())
            .then(data => {
                return data;
            })
            .catch(error => {
                console.error('API 요청 중 오류 발생:', error);
            });
}
