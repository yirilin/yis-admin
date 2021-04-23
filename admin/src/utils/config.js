import { store } from '@/store/index'
export const getDict = async (type) => {
    await store.dispatch("config/getConfig", type)
    return store.getters["config/getConfig"][type]
}