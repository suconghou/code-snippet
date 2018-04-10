// https://github.com/selfagency/typa/blob/master/src/typa.js



export const isFunction = (value) => {
	return typeof value === 'function'
}

export const isObject = (value) => {
	return value && typeof value === 'object' && value.constructor === Object
}

export const isString = (value) => {
	return typeof value === 'string' || value instanceof String
}

export const isArray = (value) => {
	return value && typeof value === 'object' && value.constructor === Array
}

export const isRegex = (value) => {
	return value && typeof value === 'object' && value.constructor === RegExp
}

export const isEmpty = (value) => {
	return (isString(value) && value === '') || (isArray(value) && value.length === 0) || (isObject(value) && Object.keys(value).length === 0)
}
