import service from '@/utils/request'

// 创建Config
export const createConfig = (data) => {
    return service({
        url: "/config",
        method: 'POST',
        data
    })
}

// 删除Config
export const deleteConfig = (id) => {
    return service({
        url: `/config/${id}`,
        method: 'DELETE',
    })
}

// 更新Config
export const updateConfig = (id, data) => {
    return service({
        url: `/config/${id}`,
        method: 'PUT',
        data
    })
}

// 用id或者type查询Config
export const findConfig = (type) => {
    return service({
        url: `/config/find/${type}`,
        method: 'GET',
    })
}

// 分页获取Config列表
export const getConfigList = (params) => {
    return service({
        url: "/config/list",
        method: 'GET',
        params
    })
}