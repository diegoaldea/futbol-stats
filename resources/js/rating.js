// Calificación de partido en escala 0-10.
//
// Combina CALIDAD (neto = suma de valor×puntos) con PARTICIPACIÓN (cantidad de
// acciones): a igual calidad, quien participó más no queda por debajo del que
// participó poco. El peso de participación es chico, así el neto sigue mandando.
//
// Todo configurable acá arriba.
export const RATING_BASE = 6.0; // rendimiento neutro
export const RATING_MAX = 10.0; // tope
export const RATING_FLOOR = 1.0; // piso
export const RATING_UMBRAL = 35; // "score" (neto + participación) que equivale a un 10
export const PARTICIPATION_WEIGHT = 0.05; // cuánto aporta cada acción por participar

export function matchRating(neto, actions = 0) {
    const score = neto + PARTICIPATION_WEIGHT * actions;
    const r = RATING_BASE + (RATING_MAX - RATING_BASE) * (score / RATING_UMBRAL);
    return Number(Math.min(RATING_MAX, Math.max(RATING_FLOOR, r)).toFixed(2));
}

// --- Rating "Momentum" (con inercia/karma) ---
//
// Procesa las acciones EN ORDEN. Mantiene un "karma" (racha reciente). Cuando una
// acción va EN CONTRA del karma, su efecto se amortigua:
//   - Venís bien (karma+): un error resta menos.
//   - Venís mal (karma-): una buena acción sube poco (no te salva de una).
// Así el puntaje no pega saltos bruscos en los extremos.
export const MOM_BASE = 6.0; // arranque neutro
export const MOM_MAX = 10.0;
export const MOM_FLOOR = 1.0;
export const MOM_RATE = 0.12; // qué tan rápido se acerca a los topes (más alto = más rápido)
export const KARMA_INFLUENCE = 0.5; // cuánto amortigua el karma a las acciones opuestas
export const KARMA_DECAY = 0.9; // 1 = racha de todo el partido; <1 = solo lo reciente

// points: lista de puntos de cada acción, EN ORDEN cronológico.
// El puntaje se acerca a 10 (buenas) o a 1 (malas) de forma asintótica: cuanto
// más cerca del tope, menos lo mueve cada acción → no se satura y diferencia.
export function momentumRating(points = []) {
    let score = MOM_BASE;
    let karma = 0;

    for (const p of points) {
        const opposing = (p > 0 && karma < 0) || (p < 0 && karma > 0);
        const cushion = opposing ? 1 / (1 + KARMA_INFLUENCE * Math.abs(karma)) : 1;
        const effect = p * cushion;

        if (effect >= 0) {
            score += effect * MOM_RATE * (MOM_MAX - score);
        } else {
            score += effect * MOM_RATE * (score - MOM_FLOOR);
        }

        karma = karma * KARMA_DECAY + p;
    }

    return Number(Math.min(MOM_MAX, Math.max(MOM_FLOOR, score)).toFixed(2));
}
