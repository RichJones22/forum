import { mount } from 'vue-test-utils';
import  expect from 'expect';
import Collections from '../../resources/assets/js/mixins/collections';

// - page is not stored on the collections.js object.  It is derived from the
//   locations object.
// - But, the collections.js object to send a PageChangeEvent, which will both
//   increment and decrement the current page of location object.
// - since we don't have access to the location object here test both the
//   increment and decrement behavior using the default page of 1.

describe('Collections', () => {
    let wrapper;

    beforeEach (() => {
       wrapper = mount(Collections);
    });

    it ('starts out with a length of 0', () => {
       expect(wrapper.vm.items.length).toBe(0);
    });

    it ('adds one item to yield a length of 1', () => {
       wrapper.vm.add('bob');
       expect(wrapper.vm.items.length).toBe(1);
    });

    it ('removes one item to yield a length of 0', () => {
        wrapper.vm.add('bob');
        expect(wrapper.vm.items.length).toBe(1);
        wrapper.vm.remove('bob');
        expect(wrapper.vm.items.length).toBe(0);
    });

    it('starts at page 1', () => {
        expect(wrapper.vm.page()).toEqual(1);
    });

    it ('increments the page number when length is > 5', () => {

        expect(wrapper.vm.items.length).toBe(0);

        wrapper.vm.add(1);
        wrapper.vm.add(2);
        wrapper.vm.add(3);
        wrapper.vm.add(4);
        wrapper.vm.add(5);
        wrapper.vm.add(6);

        expect(wrapper.vm.items.length).toBe(6);

        expect(wrapper.emitted().PageChangeEvent[0]).toEqual([2]);
    });

    it ('page count remains at 1 from 1 to 5', () => {

        expect(wrapper.vm.items.length).toBe(0);

        wrapper.vm.add('bob1');
        wrapper.vm.add('bob2');
        wrapper.vm.add('bob3');
        wrapper.vm.add('bob4');
        wrapper.vm.add('bob5');
        wrapper.vm.add('bob6');

        expect(wrapper.vm.items.length).toBe(6);

        expect(wrapper.emitted().PageChangeEvent[0]).toEqual([2]);

        wrapper.vm.remove('bob1');
        expect(wrapper.emitted().PageChangeEvent[1]).toEqual([1]);
        wrapper.vm.remove('bob2');
        expect(wrapper.emitted().PageChangeEvent[2]).toEqual([1]);
        wrapper.vm.remove('bob3');
        expect(wrapper.emitted().PageChangeEvent[3]).toEqual([1]);
        wrapper.vm.remove('bob4');
        expect(wrapper.emitted().PageChangeEvent[4]).toEqual([1]);
        wrapper.vm.remove('bob5');
        expect(wrapper.emitted().PageChangeEvent[5]).toEqual([1]);
    });

    it ('decrements the page number when length is == 0', () => {

        // - here we just want to show that we can decrement the page.
        // - page is set to 1.
        // - we add 'bob1' to the items array and then remove 'bob1' which
        //   cause the page count of 1 to be decremented to 0.
        // - this is our approach since page number is derived from the
        //   location object, and here in this test we don't have
        //   a location object.

        expect(wrapper.vm.page()).toEqual(1);
        expect(wrapper.vm.items.length).toBe(0);

        wrapper.vm.add('bob1');
        expect(wrapper.vm.items.length).toBe(1);

        wrapper.vm.remove('bob1');
        expect(wrapper.vm.items.length).toBe(0);

        // console.log(wrapper.emitted());

        expect(wrapper.emitted().PageChangeEvent[0]).toEqual([0]);
    });
});