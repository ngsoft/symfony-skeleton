import {encode, getClass, isEmpty, isFunction, isPlainObject, isString, uniqid} from "../utils";
import LocalStore from "./webstore";
import Datastore from "./datastore";
import EventManager from "../utils/event-manager";


const
    entities = new Map,
    hooks = new Map,
    models = {};
let datastore;


function hydrateModel(type, data, validate = true) {

    if (!models[type]) {
        throw new TypeError(`Invalid Model type ${type}`);
    }

    const i = Object.create(models[type]);
    if (isEmpty(data.id)) {
        data.id = uniqid();
    } else {
        uniqid.add(data.id);
    }

    EventManager.mixin(i, false);

    validate && i.validate(data);

    for (let prop in data) {
        i[prop] = data[prop];
    }
    return i;
}


function initializeModel(model) {

    const type = getClass(model);

    if (!hooks.has(type)) {

        // initialize entity cache localStorage
        model.dataStore.getItem(model.key, () => []).forEach(item => {
            const entity = hydrateModel(type, item);
            entities.set(entity.id, entity);
        });
        hooks.set(type, model.dataStore.hook(model.key, []));
    }

}


/**
 * A Base Model that Uses a DataStore as Database
 *
 * @abstract
 *
 * @property {Function} trigger {@link module:EventManager.trigger}
 * @property {Function} on {@link module:EventManager.on}
 * @property {Function} one {@link module:EventManager.one}
 * @property {Function} off {@link module:EventManager.off}
 */
export default class Model {


    /**
     * @return {Datastore}
     */
    static get dataStore() {
        return datastore ??= LocalStore;
    }

    static set dataStore(/** @type DataStore */ store) {

        if (!(store instanceof Datastore)) {
            throw new TypeError('Invalid DataStore');
        }
        datastore = store;
    }

    /**
     * @return {string}
     */
    static get key() {
        return 'Model' + getClass(this);
    }

    /**
     * @type {string|null}
     */
    id = null;


    static get hook() {
        const type = getClass(this);
        if (type === 'Model') {
            throw new Error('Cannot use Model.hook directly.');
        }

        initializeModel(this);
        return hooks.get(type);
    }

    /**
     * Register an instance of model to be able to hydrate it
     * @param {Model} model
     */
    static register(model) {

        if (!(model instanceof Model)) {
            throw new TypeError('Invalid model supplied.');
        }
        models[getClass(model)] = model;

    }

    /**
     * @param {Model|object} data
     */
    static add(data = {}) {

        const type = getClass(this);

        if (type === 'Model') {
            throw new Error('Cannot call Model.add() directly.');
        }

        initializeModel(this);


        if (isPlainObject(data) && !isEmpty(data)) {
            data = hydrateModel(type, data, false);
        }

        if (getClass(data) === getClass(this)) {
            EventManager.mixin(data, false);

            if (isEmpty(data.id)) {
                data.id = uniqid();
            }

            if (!entities.has(data.id)) {
                data.validate(data);
                entities.set(data.id, data);
                // update localstorage
                this.hook.set(this.findAll().map(x => x.extract()));
                data.onAdd();
                data.trigger('add');
            }
        }

    }


    /**
     * @param {Model} entity
     */
    static update(entity) {
        const type = getClass(this);

        if (type === 'Model') {
            throw new Error('Cannot call Model.update() directly.');
        }

        if (!(entity instanceof Model)) {
            throw new TypeError('Invalid entity provided');
        }

        if (!entity.id) {
            throw new TypeError(`${type}.id is not defined.`);
        }

        initializeModel(this);
        entity.validate(entity);
        entities.set(entity.id, entity);
        // update storage
        this.hook.set(this.findAll().map(x => x.extract()));
        entity.onUpdate();
        entity.trigger('update');
    }

    /**
     * @param {string|object} id
     */
    static findById(id) {

        if (getClass(this) === 'Model') {
            throw new Error('Cannot call Model.add() directly.');
        }

        if (typeof id === 'object' && null !== id) {
            id = id.id;
        }

        if (!isString(id) || isEmpty(id)) {
            throw new TypeError(`Invalid argument id`);
        }

        initializeModel(this);

        const result = entities.get(id);

        if (getClass(result) === getClass(this)) {
            return result;
        }

        return null;
    }

    /**
     * @return {Array}
     */
    static findAll() {
        if (getClass(this) === 'Model') {
            throw new Error('Cannot call Model.add() directly.');
        }
        initializeModel(this);

        const type = getClass(this);
        return [...entities.values()].filter(
            item => getClass(item) === type
        );
    }

    /**
     * @param {function|object} constrain
     * @return {Array}
     */
    static find(constrain = {}) {


        if (isFunction(constrain)) {
            return this.findAll().filter(constrain);
        }

        if (isPlainObject(constrain)) {

            let all = this.findAll();

            if (isEmpty(constrain)) {
                return all;
            }

            for (let prop in constrain) {
                const value = constrain[prop];
                if (isFunction(value)) {
                    all = all.filter(item => value(item[prop]));
                } else {
                    all = all.filter(item => encode(item[prop]) === encode(value));
                }
            }

            return all;
        }

        throw new TypeError('Invalid constrain provided');
    }

    /**
     * @param {string|Model} id
     * @return {void}
     */
    static remove(id) {

        const type = getClass(this);
        if (getClass(this) === 'Model') {
            throw new Error('Cannot call Model.remove() directly.');
        }

        if (id instanceof Model) {

            if (getClass(id) !== type) {
                throw new TypeError(`Invalid entity of type ${getClass(id)} provided`);
            }
            id = id.id;
        }

        if (!isString(id)) {
            throw new TypeError('Invalid id supplied.');
        }

        initializeModel(this);

        const
            entity = this.findById(id),
            newData = this.findAll().filter(x => x.id !== id);

        if (entity) {
            entities.delete(id);
            this.hook.set(newData);
            entity.onDelete();
            entity.trigger('delete');
        }


    }


    /**
     * @abstract
     * @param {Object|Model} data
     * @return {void}
     */
    validate(data) {
        throw new TypeError(getClass(this) + 'validate() not implemented');
    }

    /**
     * @abstract
     * @return {object}
     */
    extract() {
        throw new Error(getClass(this) + '.extract() not implemented');
    }


    onAdd() {
    }


    onUpdate() {
    }


    onDelete() {
    }
}