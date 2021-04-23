import { findConfig } from '@/api/config'

export const config = {
    namespaced: true,
    state: {
        configMap: {},
    },
    mutations: {
        setConfigMap(state, configMap) {
            state.configMap = { ...state.configMap, ...configMap }
        },
    },
    actions: {
        // 从后台获取动态路由
        async getConfig({ commit, state }, type) {
            if (state.configMap[type]) {
                return state.configMap[type]
            } else {
                const res = await findConfig(type)
                if (res.code == 200) {
                    const configMap = {}
                    const dict = []
                    res.data.configValue.config_values && res.data.configValue.config_values.map(item => {
                        dict.push({
                            label: item.label,
                            value: item.value
                        })
                    })
                    configMap[res.data.configValue.name] = dict//todo type
                    commit("setConfigMap", configMap)
                    return state.configMap[type]
                }
            }
        }
    },
    getters:{
        getConfig(state){
            return state.configMap
        }
    }
}