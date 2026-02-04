@if(config('app.env') !== 'production' && config('app.debug') === true)
<div id="dev-debug-badge" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: #00ff00; padding: 12px 16px; border-radius: 8px; font-family: 'Courier New', monospace; font-size: 11px; box-shadow: 0 4px 12px rgba(0,0,0,0.5); border: 1px solid #00ff00; max-width: 400px; backdrop-filter: blur(10px);">
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; border-bottom: 1px solid #00ff00; padding-bottom: 6px;">
        <span style="background: #00ff00; color: #000; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 10px;">DEV</span>
        <span style="color: #ffcc00; font-weight: bold;">{{ $info['env'] }}</span>
        @if($info['debug'])
        <span style="background: #ff4444; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 9px;">DEBUG</span>
        @endif
    </div>
    
    <div style="display: grid; gap: 4px;">
        <div style="display: flex; gap: 6px;">
            <span style="color: #888; min-width: 60px;">Route:</span>
            <span style="color: #00ffff; word-break: break-all;">{{ $info['route'] }}</span>
        </div>
        
        <div style="display: flex; gap: 6px;">
            <span style="color: #888; min-width: 60px;">Action:</span>
            <span style="color: #ff69b4;">{{ $info['action'] }}</span>
        </div>
        
        <div style="display: flex; gap: 6px;">
            <span style="color: #888; min-width: 60px;">URL:</span>
            <span style="color: #90ee90; font-size: 10px; word-break: break-all;">{{ Str::limit($info['url'], 50) }}</span>
        </div>
        
        <div style="display: flex; gap: 6px; margin-top: 4px; padding-top: 6px; border-top: 1px dotted #444;">
            <span style="color: #888; min-width: 60px;">Commit:</span>
            <span style="color: #ffa500; font-weight: bold;">{{ $info['commit'] }}</span>
        </div>
        
        <div style="display: flex; gap: 6px;">
            <span style="color: #888; min-width: 60px;">Build:</span>
            <span style="color: #9370db;">{{ $info['build'] }}</span>
        </div>
        
        <div style="display: flex; gap: 6px;">
            <span style="color: #888; min-width: 60px;">Time:</span>
            <span style="color: #87ceeb; font-size: 10px;">{{ $info['timestamp'] }}</span>
        </div>
    </div>
    
    <div style="margin-top: 8px; padding-top: 6px; border-top: 1px solid #444; text-align: center;">
        <button onclick="document.getElementById('dev-debug-badge').style.display='none'" style="background: #ff4444; color: #fff; border: none; padding: 4px 12px; border-radius: 4px; cursor: pointer; font-size: 10px; font-weight: bold;">Hide</button>
    </div>
</div>
@endif
