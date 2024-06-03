import { useEffect, useState } from "react";
import { API_URL } from '../constants';
import { formatName } from '../utils';

export function Navigation({ pagination, setCurrentURL, stats, currentURL }) {

    const [byStat, setByStat] = useState({ id: null, setOrder: 'desc' });
    const [types, setTypes] = useState([])

    const handleByStatClick = (statId) => {

        let orderBy = { id: null, setOrder: null }
        const urlParams = new URLSearchParams(currentURL.split('?')[1])
        const isSameStat = byStat.id === statId
        const isOrderDesc = byStat.setOrder === 'desc'
        const selectedType = urlParams.get('type');

        if (isSameStat) {
            if (isOrderDesc) {
                orderBy = { id: statId, setOrder: 'asc' }
                urlParams.set('order', 'asc');
                urlParams.set('stat', statId);
            } else {
                urlParams.delete('order');
                urlParams.delete('stat');
            }
        } else {
            orderBy = { id: statId, setOrder: 'desc' }
            urlParams.set('order', 'desc');
            urlParams.set('stat', statId);
        }

        if (selectedType) {
            urlParams.set('type', selectedType);
        }
        urlParams.set('page', 1)

        setByStat(orderBy)
        setCurrentURL(`${API_URL}?${urlParams.toString()}`)
    }

    const handleByTypeClick = (typeId) => {
        const urlParams = new URLSearchParams(currentURL.split('?')[1])
        const selectedType = urlParams.get('type') === ""

        selectedType ? urlParams.delete('type') : urlParams.set('type', typeId);
        urlParams.set('page', 1)
        setCurrentURL(`${API_URL}?${urlParams.toString()}`)
    }

    useEffect(() => {
        fetch(`${API_URL}?types`)
            .then(response => response.json())
            .then(data => setTypes(data.results))
    }, [])

    return (
        <nav className="fixed top-0 right-auto left-auto w-full text-white bg-slate-700">
            <div className="flex justify-around items-center mt-5 mx-auto max-w-[500px] sm:w-4/5">
                {pagination?.previous && (
                    <button onClick={() => setCurrentURL(pagination.previous)} className="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded active:bg-blue-700">←</button>
                )}
                <h1 className="text-center">
                    <select
                        onChange={(e) => handleByTypeClick(e.target.value)}
                        className="bg-gray-400 text-black p-2 rounded">
                        <option value="">All</option>
                        {types.map(type => (
                            <option key={type.id} value={type.id}>{formatName(type.name)}</option>
                        ))}
                    </select>
                </h1>
                {pagination?.next && (
                    <button onClick={() => setCurrentURL(pagination.next)} className="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded active:bg-blue-700">→</button>
                )}
            </div>
            <div className="flex justify-center flex-wrap w-fit mx-auto my-5 gap-1">
                {stats.map(stat => (
                    <button onClick={() => handleByStatClick(stat.id)} key={stat.id} className={`text-sm p-2 rounded ${byStat.id === stat.id ? 'bg-red-500' : 'bg-gray-400'}`}>
                        {formatName(stat.name)} {byStat.id === stat.id && (byStat.setOrder === 'desc' ? '↓' : '↑')}
                    </button>
                ))}
            </div>
        </nav>
    );
}