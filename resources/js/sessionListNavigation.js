const positivePage = value => /^\d+$/.test(String(value)) && Number.isSafeInteger(Number(value)) && Number(value) > 0
    ? Number(value) : 1;

export function readSessionListFilters(search) {
    const params = new URLSearchParams(search);
    return {
        track_id: params.get('track_id') || '',
        type_id: params.get('type_id') || '',
        all_statuses: params.get('all_statuses') === '1',
        not_scheduled: params.get('not_scheduled') === '1',
        keyword: params.get('keyword') || '',
        page: positivePage(params.get('page')),
    };
}

export function sessionListUrl(pathname, filters, page = 1) {
    const params = new URLSearchParams();
    for (const key of ['track_id', 'type_id', 'keyword']) {
        if (filters[key]) params.set(key, filters[key]);
    }
    for (const key of ['all_statuses', 'not_scheduled']) {
        if (filters[key]) params.set(key, '1');
    }
    const currentPage = positivePage(page);
    if (currentPage > 1) params.set('page', currentPage);
    const query = params.toString();
    return pathname + (query ? '?' + query : '');
}

export function sessionListReturnUrl(search, listLink, origin) {
    const listPath = new URL(listLink, origin).pathname;
    const candidate = new URLSearchParams(search).get('return_to');
    if (candidate) {
        try {
            const url = new URL(candidate, origin);
            if (url.origin === origin && url.pathname === listPath) {
                const filters = readSessionListFilters(url.search);
                return sessionListUrl(listPath, filters, filters.page);
            }
        } catch (_) {
            // Direct links and invalid return destinations use the plain list.
        }
    }
    return listPath;
}
