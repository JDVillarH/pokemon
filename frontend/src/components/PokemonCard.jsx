import { API_URL } from "../constants";
import { formatName } from "../utils";

export function PokemonCard({ id, name, image, stats, types, setCurrentDetailURL }) {

    const allowedStats = ['hp', 'attack', 'defense', 'speed'];
    return (
        <button onClick={() => setCurrentDetailURL(`${API_URL}?id=${id}`)}>
            <article className='w-[325px] h-[450px] border-1 rounded-xl shadow-xl my-0 mx-auto'>
                <header className='py-3 px-3 bg-sky-200/50'>
                    <img
                        className='my-0 mx-auto size-52 rounded-full border-2 border-gray-500 bg-slate-300/70 p-2 object-cover'
                        src={image} alt={`Imagen de ${name}`} />
                </header>
                <article>
                    <div className="text-center">
                        <h1 className='text-xl mt-2 break-words mb-3'>{formatName(name)}</h1>
                        {types.map(type => (
                            <span key={type.id} className="bg-sky-500 text-white rounded-full px-2 py-1 mx-1">{formatName(type.name)}</span>
                        ))}
                    </div>
                    <hr className='border-t-2 my-3' />
                    <div className='flex justify-between px-5'>
                        <div className="text-left">
                            {stats.map(stat => (
                                allowedStats.includes(stat.name) && <h3 key={stat.id} className='font-semibold'>{formatName(stat.name)}</h3>
                            ))}
                        </div>
                        <div>
                            {stats.map(stat => (
                                allowedStats.includes(stat.name) && <p key={stat.id} className='text-gray-400'>{stat.base_stat}</p>
                            ))}
                        </div>
                    </div>
                </article>
            </article>
        </button>
    );
}