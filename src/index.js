import {registerBlockType} from '@wordpress/blocks';
import { RadioControl } from '@wordpress/components';
import { RangeControl } from '@wordpress/components';
import { TextControl } from '@wordpress/components';


registerBlockType('crypt/price-block', {
    title: '暗号通貨価格',
    icon: 'chart-line',
    category: 'common',
    attributes: {
        contentBefore: {
            type: 'string',
        },
        contentAfter: {
            type: 'string',
        },
        option: {
            type: 'string',
        },
        floatnum: {
            type: 'integer',
        },
    },

    edit: ( props ) => {
        const {
            className,
            attributes: {
                contentBefore,
                contentAfter,
                option,
                floatnum
            },
            setAttributes,
        } = props;
        return (
            <>
            <div className={ className }>
            <TextControl
        value={ contentBefore }
        label={ "価格の前" }
        onChange={(newTextBefore)=>setAttributes({contentBefore: newTextBefore})}
        />
            <RadioControl
        label="表示する価格"
        help=""
        selected={ option }
        options={ [
                { label: '終値', value: 'last' },
        { label: '過去24時間の加重平均', value: 'vwap' },
    ] }
        onChange={(newValue)=>{setAttributes({option: newValue})}}
        />
        <RangeControl
        label="小数点何位まで"
        value={ floatnum }
        onChange={ ( newNumber ) => setAttributes( { floatnum: newNumber } ) }
        min={ 0 }
        max={ 9 }
        />
        <TextControl
        value={ contentAfter }
        label={ "価格の後" }
        onChange={(newTextAfter)=>setAttributes({contentAfter: newTextAfter})}
        />
        </div>
        </>
    );
    },
    save: () => null,
});