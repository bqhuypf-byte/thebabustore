import test from 'node:test';import assert from 'node:assert/strict';import {resolveKeyCode,ScrollStats,KeyboardImeMonitor} from '../../public/js/device-tester.js';
test('scroll counters remain functional',()=>{const scroll=new ScrollStats();scroll.record(-100);scroll.record(100);assert.equal(scroll.up,1);assert.equal(scroll.down,1);scroll.reset();assert.equal(scroll.up,0);});
test('keyboard resolution is retained',()=>assert.equal(resolveKeyCode({code:'KeyQ',key:'q'}),'KeyQ'));

test('IME warning recognises Vietnamese characters and browser composition signals',()=>{
 const m=new KeyboardImeMonitor();for(const key of ['â','đ','ế','â'])assert.equal(m.observe({type:'keydown',key},100),true);
 assert.equal(m.observe({type:'keydown',key:'Process',keyCode:229},100),true);
 assert.equal(m.observe({type:'compositionstart'},100),true);
 assert.equal(m.observe({type:'beforeinput',data:'đ'},100),true);
});
test('Backspace following a recent Vietnamese conversion pattern warns, ordinary Backspace does not',()=>{
 const m=new KeyboardImeMonitor();assert.equal(m.observe({type:'keydown',key:'Backspace',code:'Backspace'},0),false);
 m.observe({type:'keydown',key:'d'},100);m.observe({type:'keydown',key:'d'},150);
 assert.equal(m.observe({type:'keydown',key:'Backspace',code:'Backspace'},180),true);
 m.reset();m.observe({type:'keydown',key:'a'},100);m.observe({type:'keydown',key:'a'},150);
 assert.equal(m.observe({type:'keydown',key:'Backspace',code:'Backspace'},500),false);
});
test('plain typing, keyup, modifiers and reset do not create conversion warnings',()=>{
 const m=new KeyboardImeMonitor();assert.equal(m.observe({type:'keydown',key:'a'},100),false);
 assert.equal(m.observe({type:'keyup',key:'a'},110),false);assert.equal(m.observe({type:'keydown',key:'Backspace',code:'Backspace'},130),false);
 m.observe({type:'keydown',key:'d',ctrlKey:true},200);m.observe({type:'keydown',key:'d',ctrlKey:true},220);
 assert.equal(m.observe({type:'keydown',key:'Backspace'},230),false);
 m.observe({type:'keydown',key:'d'},300);m.observe({type:'keydown',key:'d'},320);m.reset();assert.equal(m.observe({type:'keydown',key:'Backspace'},340),false);
});
