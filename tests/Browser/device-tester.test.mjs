import test from 'node:test';
import assert from 'node:assert/strict';
import { ClickSession, summarizeIntervals } from '../../public/js/device-tester.js';

test('click timing starts on the first click and excludes the exact deadline', () => {
    const session = new ClickSession();
    session.arm(5);
    assert.equal(session.result(9000).phase, 'armed');
    session.click(10000);
    session.click(11000);
    session.click(14999);
    session.click(15000);
    assert.deepEqual(session.result(20000), { phase: 'complete', count: 3, remaining: 0, progress: 100, cps: 0.6 });
});

test('late timer callbacks do not extend a ten-second test', () => {
    const session = new ClickSession();
    session.arm(10);
    session.click(2000);
    session.click(11999);
    session.advance(30000);
    assert.equal(session.result(30000).cps, 0.2);
    assert.equal(session.result(30000).remaining, 0);
});

test('cancel and rearm clear counts and start time', () => {
    const session = new ClickSession();
    session.arm(5);
    session.click(100);
    session.cancel();
    assert.equal(session.result(200).count, 0);
    session.arm(10);
    session.click(9000);
    assert.equal(session.result(10000).count, 1);
    assert.equal(session.result(10000).remaining, 9);
});

test('movement rate uses elapsed timestamps, not render frames', () => {
    const times = Array.from({ length: 126 }, (_, i) => i * 8);
    assert.deepEqual(summarizeIntervals(times, 1000), { hz: 125, mean: 8, min: 8, max: 8 });
    const fast = Array.from({ length: 1601 }, (_, i) => i * 0.125);
    assert.equal(summarizeIntervals(fast, 200).hz, 8000);
});

test('stale, duplicate, invalid and insufficient movement samples are excluded', () => {
    assert.equal(summarizeIntervals([1, 2, 3], 3), null);
    assert.equal(summarizeIntervals([1, 2, 3], 5000), null);
    const valid = Array.from({ length: 21 }, (_, i) => 1000 + i * 10);
    const noisy = [-2000, NaN, ...valid.flatMap(time => [time, time]), Infinity, 9000];
    assert.deepEqual(summarizeIntervals(noisy, 1200), { hz: 100, mean: 10, min: 10, max: 10 });
});
