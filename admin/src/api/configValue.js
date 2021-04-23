import service from '@/utils/request'

// 创建ConfigValue
export const createConfigValue = (data) => {
    return service({
        url: "/configValue",
        method: 'POST',
        data
    })
}

// 删除ConfigValue
export const deleteConfigValue = (id) => {
    return service({
        url: `/configValue/${id}`,
        method: 'DELETE',
    })
}

// 更新ConfigValue
export const updateConfigValue = (id, data) => {
    return service({
        url: `/configValue/${id}`,
        method: 'put',
        data
    })
}

// 用id查询ConfigValue
export const findConfigValue = (id) => {
    return service({
        url: `/configValue/find/${id}`,
        method: 'GET',
    })
}

// 分页获取ConfigValue列表
export const getConfigValueList = (params) => {
    return service({
        url: "/configValue/list",
        method: 'GET',
        params
    })
}