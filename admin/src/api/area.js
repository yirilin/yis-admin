import service from '@/utils/request'

// 创建Area
export const createArea = (data) => {
    return service({
        url: "/area",
        method: 'POST',
        data
    })
}

// 更具ID或IDS 删除Area
export const deleteArea = (id) => {
    return service({
        url: `/area/${id}`,
        method: 'DELETE',
    })
}

// 更新Area
export const updateArea = (id, data) => {
    return service({
        url: `/area/${id}`,
        method: 'PUT',
        data
    })
}

// 根据idArea
export const findArea = (type) => {
    return service({
        url: `/area/find/${type}`,
        method: 'GET',
    })
}

// 分页获取Area列表
export const getAreaList = (params) => {
    return service({
        url: "/area/list",
        method: 'GET',
        params
    })
}