export default function pickBanLobby(lobbyCode, user) {
    return {
        maps: [
            { name: 'mirage', status: 'available' },
            { name: 'inferno', status: 'available' },
            { name: 'nuke', status: 'available' },
            { name: 'overpass', status: 'available' },
            { name: 'vertigo', status: 'available' },
            { name: 'ancient', status: 'available' },
            { name: 'anubis', status: 'available' },
        ],
        canBan: false,
        finalMap: null,
        init() {
            Echo.channel(`lobby.${lobbyCode}`)
                .listen('MapBanned', (e) => {
                    this.maps = this.maps.map(map => {
                        if (map.name === e.map) {
                            map.status = 'banned';
                        }
                        if (e.finalMap && map.name === e.finalMap) {
                            map.status = 'picked';
                            this.finalMap = e.finalMap;
                        }
                        return map;
                    });
                    this.updateTurn();
                });

            this.updateTurn();
        },
        banMap(mapName) {
            axios.post(`/lobby/${lobbyCode}/ban-map`, { map: mapName }).then(() => {
                this.canBan = false;
            });
        },
        updateTurn() {
            axios.get(`/lobby/${lobbyCode}/current-turn`).then(({ data }) => {
                this.canBan = data.user_id === user.id;
            });
        }
    }
}
