import {isEmpty} from "../utils";
import {DataStore} from "./datastore";


const
    SHARED = 'shared',
    Store = new Map();


export class MemoryStore extends DataStore {


    get store() {
        return Store.get(this.key(''));
    }


    constructor(prefix = SHARED) {

        if (isEmpty(prefix)) {
            throw new TypeError("prefix cannot be empty");
        }

        super(prefix);


        if (!Store.has(prefix)) {
            Store.set(this.key(''), {});
        }

    }


    get keys() {
        return Object.keys(this.store);
    }


    getItem(/** @type {string} */name, defaultValue = null) {
        return super.getItem(name, this.store[name] ?? defaultValue);
    }

    setItem(/** @type {string} */name, value) {

        if (value === null) {
            delete this.store[name];
        } else {
            this.store[name] = value;
        }

        return value;

    }

}


export default new MemoryStore();



