import { useEffect, useState } from "react";
import { PokemonCard } from "./components/PokemonCard";
import { Navigation } from "./components/Navigation";
import { API_URL } from './constants';
import { PokemonDetail } from "./components/PokemonDetail";

export function App() {

    const [pokemon, setPokemon] = useState([])
    const [pokemonDetail, setPokemonDetail] = useState([])

    const [currentDetailURL, setCurrentDetailURL] = useState('')
    const [currentURL, setCurrentURL] = useState(`${API_URL}?page=1`)

    const results = pokemon?.results || []
    const stats = results?.[0]?.stats || [];

    useEffect(() => {
        fetch(currentURL)
            .then(response => response.json())
            .then(data => setPokemon(data))
    }, [currentURL])

    useEffect(() => {
        if (currentDetailURL.length === 0) return;

        fetch(currentDetailURL)
            .then(response => response.json())
            .then(data => {
                setPokemonDetail(data.results);
                document.querySelector('dialog').showModal();
                document.querySelector('body').style.overflow = 'hidden';
            })
    }, [currentDetailURL])

    return (
        <main>
            <Navigation currentURL={currentURL} pagination={pokemon.pagination} setCurrentURL={setCurrentURL} stats={stats} />
            <section className="flex justify-center flex-wrap lg:w-3/5 sm:4/5 lg:mt-40 md:mt-52 mt-60 mb-10 mx-auto gap-10">
                {results.map(p => (
                    <PokemonCard key={p.id} id={p.id} name={p.name} image={p.image} stats={p.stats} types={p.types} setCurrentDetailURL={setCurrentDetailURL} />
                ))}
            </section>
            <PokemonDetail pokemonDetail={pokemonDetail} setCurrentDetailURL={setCurrentDetailURL} />
        </main>
    );
}