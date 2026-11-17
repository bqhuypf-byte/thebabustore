import test from 'node:test';
import assert from 'node:assert/strict';
import { WheelDirectionSession, ClickAnomalySession, ScrollStats, ClickSession, summarizeIntervals, countsForSession, resolveKeyCode } from '../../public/js/device-tester.js';

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

test('both side buttons and all other buttons count in the default session', () => {
    const session = new ClickSession();
    session.arm(5, 'all');
    for (let button = 0; button <= 4; button += 1) session.click(1000 + button * 100, button);
    session.click(1500, 3);
    session.click(1600, 4);
    assert.equal(session.result(6000).count, 7);
    assert.equal(session.result(6000).cps, 1.4);
    assert.equal(countsForSession(3, 'all'), true);
    assert.equal(countsForSession(4, 'all'), true);
    assert.equal(countsForSession(-1, 'all'), false);
    assert.equal(countsForSession(5, 'all'), false);
});

test('a side-button-only session starts only on that button', () => {
    const session = new ClickSession();
    session.arm(5, '3');
    session.click(100, 0);
    session.click(200, 4);
    assert.equal(session.result(300).phase, 'armed');
    session.click(1000, 3);
    session.click(1100, 0);
    session.click(1200, 4);
    session.click(1300, 3);
    assert.equal(session.result(6000).count, 2);
});

test('media keys without a physical code use the observed media key value', () => {
    assert.equal(resolveKeyCode({code:'',key:'AudioVolumeUp'}), 'AudioVolumeUp');
    assert.equal(resolveKeyCode({code:'Unidentified',key:'MediaPlayPause'}), 'MediaPlayPause');
    assert.equal(resolveKeyCode({code:'KeyQ',key:'a'}), 'KeyQ');
    assert.equal(resolveKeyCode({code:'',key:'a'}), null);
});

test('scroll distance keeps units separate, counts direction and resets completely', () => {
    const scroll = new ScrollStats();
    scroll.record(-120, 0); scroll.record(80, 0);
    scroll.record(-3, 1); scroll.record(1, 2);
    assert.equal(scroll.up, 2); assert.equal(scroll.down, 2);
    assert.deepEqual(scroll.distance, [200, 3, 1]);
    assert.deepEqual(scroll.last, { deltaY: 1, deltaMode: 2 });
    for (const [delta, mode] of [[0,0], [NaN,0], [Infinity,0], [10,3]]) assert.equal(scroll.record(delta,mode), false);
    assert.deepEqual(scroll.distance, [200, 3, 1]);
    scroll.reset();
    assert.deepEqual([scroll.up,scroll.down,scroll.last], [0,0,null]);
    assert.deepEqual(scroll.distance,[0,0,0]);
});

test('single-click check isolates selected button and flags close presses without declaring failure', () => {
    const check = new ClickAnomalySession();
    assert.equal(check.record(0, 0), null);
    check.start(0, 80);
    assert.deepEqual(check.record(0, 1000), { interval:null, suspect:false });
    assert.equal(check.record(2, 1010), null);
    assert.deepEqual(check.record(0, 1080), { interval:80, suspect:true });
    assert.deepEqual(check.record(0, 2080), { interval:1000, suspect:false });
    assert.equal(check.suspects,1); assert.equal(check.count,3);
    check.active=false; assert.equal(check.record(0,2090),null);
    check.start(2,40); assert.equal(check.count,0); assert.equal(check.suspects,0);
    assert.deepEqual(check.record(2,3000),{interval:null,suspect:false});
    assert.equal(check.record(2,NaN),null);
    assert.equal(check.record(2,2900),null);
});

test('one-way wheel check flags opposing signs and excludes zoom and empty events', () => {
    const check = new WheelDirectionSession();
    assert.equal(check.record(100),null);
    check.start('down');
    assert.equal(check.record(120).reversed,false);
    assert.equal(check.record(-3,1).reversed,true);
    assert.equal(check.record(1,2).reversed,false);
    for (const args of [[0], [NaN], [Infinity], [-120,0,true], [1,4]]) assert.equal(check.record(...args),null);
    assert.equal(check.count,3);assert.equal(check.reversed,1);
    check.active=false; assert.equal(check.record(-120),null);
    check.start('up'); assert.equal(check.count,0);assert.equal(check.reversed,0);
    assert.equal(check.record(-120).reversed,false);
    assert.equal(check.record(120).reversed,true);
    check.reset();assert.equal(check.active,false);assert.equal(check.count,0);assert.equal(check.reversed,0);
});
