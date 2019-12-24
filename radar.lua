--  LUA script for GTA Map Viewer
--  © 2005 by Steve M.
--
--  radar screenshot generator, see scripting.txt

-- Make sure the window resolution is set to something like 128*128 and the
-- window borders are switched off!

-- the SA map is 6000*6000 units large, with 144 radar pieces

-- this is all you need to configure: -----------------------------------------
FOV       = 10    -- in degrees
map_width = 20500  -- in units
num_radar = 196   -- number of radar pieces, must have a natural square root
num_radar = 55*55   -- number of radar pieces, must have a natural square root

-- 48 is gudish
-------------------------------------------------------------------------------

num2   = math.sqrt(num_radar)
width  = map_width / num2
height = (width/2) / math.tan(math.rad(FOV/2))

SetMaxFilesPerFrame(99999)   -- load all needed files per frame
SetCamera(4, false)          -- disable distance culling
SetCamera(2, 0, -math.pi/2)  -- look down
SetCamera(6, FOV)            -- set FOV
SetGameTime(12, 00)          -- set time to noon

for i = 0, num_radar do
  x = math.mod(i, num2) * width - (map_width - width) / 2
  y = (num2 - 1 - math.floor(i / num2)) * width - (map_width - width) / 2

  --Msg('('..x..', '..y..')')

  UpdateEngineTime()
  SetCamera(1, x, y, height)
  Render()
  TakeScreenshot(string.format('radar%02d.bmp', i))
end

-- ...and back to normal
SetCamera(4, true)
SetCamera(0)
SetMaxFilesPerFrame(10)
