function fetchApi(url, options = {}) {
    const defaultOptions = {
        credentials: 'same-origin',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    };

    const config = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...(options.headers || {})
        }
    };

    return fetch(url, config)
        .then(response => {
            if (!response.ok) {
                throw new Error('API 요청이 실패했습니다.');
            }
            return response.json();
        })
        .catch(error => {
            console.error('API 요청 중 오류 발생:', error);
            throw error;
        });
}
