import service from '@/utils/request'

// 创建Article
export const createArticle = (data) => {
    return service({
        url: "/article",
        method: 'POST',
        data
    })
}

// 更具ID或IDS 删除Article
export const deleteArticle = (id) => {
    return service({
        url: `/article/${id}`,
        method: 'DELETE',
    })
}

// 更新Article
export const updateArticle = (id, data) => {
    return service({
        url: `/article/${id}`,
        method: 'PUT',
        data
    })
}

// 根据idArticle
export const findArticle = (type) => {
    return service({
        url: `/article/find/${type}`,
        method: 'GET',
    })
}

// 分页获取Article列表
export const getArticleList = (params) => {
    return service({
        url: "/article/list",
        method: 'GET',
        params
    })
}