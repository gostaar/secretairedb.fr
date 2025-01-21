export function handleOAuthRedirect() {
    const urlParams = new URLSearchParams(window.location.search);
    const authCode = urlParams.get('code');

    if (authCode) {
        sessionStorage.setItem('googleAuthCode', authCode);
        window.location.href = '/exchange-google-token';
    }
}
