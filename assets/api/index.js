import {isEmpty, isJSON, isPlainObject, isString} from "../modules/utils/index.js";

let token, tokenExpires = 0, basepath;


export const API_ENDPOINTS = {
    user: '/api/user',
};

function getBasePath() {
    return basepath ?? '';
}


function now() {
    return (new Date()).getTime();
}

async function getToken() {

    if (now() >= tokenExpires) {
        const data = await fetch(
            getBasePath() + '/profile/user-token'
        ).then(
            resp => {
                if (resp.status === 200) {
                    return resp.json();
                }
                throw new Error('Not Logged in');
            }
        ).catch(() => null);

        if (!isEmpty(data)) {
            tokenExpires = (new Date(data.expires)).getTime();
            token = data.token;
        }
    }
    return token;
}

/**
 * Check if token is valid
 * @param token
 * @return {Promise<boolean>}
 */
export async function checkToken(token) {

    return await fetch(
        getBasePath() + API_ENDPOINTS.user, {
            headers: {Authorization: `Bearer ${token}`}
        }
    ).then(resp => resp.status === 200)
        .catch(() => false);


}

/**
 * Makes an Api Call
 * @param {String} endpoint
 * @param {Object|String} params
 * @param {String} method
 * @return {Promise<Object|null>}
 */
export async function apiCall(endpoint, params = {}, method = 'GET') {

    if (isEmpty(endpoint)) {
        throw new Error('Invalid path');
    }

    let apiKey = await getToken();

    if (isEmpty(apiKey)) {
        throw new Error('Not logged in');
    }

    const headers = {
        Authorization: `Bearer ${apiKey}`,
    };

    method = method.toUpperCase();

    const url = new URL(location.origin);
    url.pathname = getBasePath() + endpoint;

    let body;

    if (method === 'GET') {

        if (isPlainObject(params)) {
            for (let prop in params) {
                if (params.hasOwnProperty(prop)) {
                    url.searchParams.set(prop, params[prop]);
                }
            }
        } else if (isString(params)) {
            url.search = params;
        }

    } else if (isPlainObject(params)) {

        headers['Content-Type'] = 'application/x-www-form-urlencoded';
        body = (new URLSearchParams(params)).toString();

    } else if (isJSON(params)) {
        headers['Content-Type'] = 'application/json';
        body = params;
    } else if (isString(params)) {
        headers['Content-Type'] = 'application/x-www-form-urlencoded';
        body = params;
    }


    return await fetch(url, {
        headers,
        body
    }).then(resp => {
        if (200 === resp.status) {
            return resp.json();
        }
        console.error(
            'Invalid status Code', resp.status,
            'for', url.pathname,
        );
        return null;
    }).catch(err => {
        console.error(err);
        return null;
    });

}