import { formatName } from "../utils"

export function PokemonDetail({ pokemonDetail, setCurrentDetailURL }) {

    const stats = pokemonDetail?.stats || []
    const moves = pokemonDetail?.moves || []
    const types = pokemonDetail?.types || []
    const abilities = pokemonDetail?.abilities || []


    const closeDialog = () => {
        document.querySelector('body').style.overflow = 'auto'
        document.querySelector('dialog').close()
        setCurrentDetailURL('')
    }

    return (
        <dialog className='fixed inset-0 z-50 lg:w-3/5 sm:size-full backdrop::bg-black/50 backdrop:backdrop-blur-sm'>
            <article className="relative bg-white m-0 rounded-sm py-5">
                <button className="sticky float-right right-5 top-5 rounded-full bg-gray-500 p-1 text-white" onClick={() => closeDialog()}>
                    <svg width="25" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M18 6l-12 12" />
                        <path d="M6 6l12 12" />
                    </svg>
                </button>
                <header className="flex justify-center flex-wrap gap-10">
                    <section className="text-center">
                        <img className="w-60" src={pokemonDetail?.image} alt={`Imagen de ${formatName(pokemonDetail?.name)}`} />
                        <h1 className="text-4xl pb-4">{formatName(pokemonDetail?.name)}</h1>
                        {types.map(type => (
                            <span key={type.id} className="bg-sky-500 text-white rounded-full px-2 py-1 mx-1">{formatName(type.name)}</span>
                        ))}
                    </section>

                    <section>
                        <h2 className="text-3xl text-center my-4">Stats</h2>
                        <div className='flex justify-between w-52'>
                            <div className="text-left">
                                {stats.map((stat, index) => (
                                    <p key={index} className='font-semibold'>{formatName(stat.name)}</p>
                                ))}
                            </div>
                            <div>
                                {stats.map((stat, index) => (
                                    <p key={index} className='text-gray-400'>{stat.base_stat}</p>
                                ))}
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2 className="text-3xl text-center my-4">Abilities</h2>
                        <div className="text-left">
                            {abilities.map((ability, key) => (
                                <h3 key={key} className='font-semibold'>{formatName(ability.name)}</h3>
                            ))}
                        </div>
                    </section>
                </header>

                <hr className="my-5" />

                <footer className="w-[90%] my-0 mx-auto">
                    <h2 className="text-3xl text-center my-4">Moves</h2>
                    <div className="flex justify-start flex-wrap flex-row gap-y-2 gap-x-3">
                        {moves.map((move, key) => (
                            <p key={key} className='font-semibold p-3 bg-slate-300 rounded-xl'>{formatName(move.name)}</p>
                        ))}
                    </div>
                </footer>

            </article>
        </dialog>
    )
}