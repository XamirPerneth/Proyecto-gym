$base = 'http://127.0.0.1:8000/api/v1'
function Call($metodo, $ruta, $cuerpo = $null) {
  $args = @{ Uri = "$base$ruta"; Method = $metodo; Headers = @{ Accept = 'application/json' }; ContentType = 'application/json; charset=utf-8'; UseBasicParsing = $true }
  if ($cuerpo) { $args.Body = [System.Text.Encoding]::UTF8.GetBytes(($cuerpo | ConvertTo-Json)) }
  try { $r = Invoke-WebRequest @args; "$metodo $ruta -> $($r.StatusCode): " + [System.Text.Encoding]::UTF8.GetString($r.RawContentStream.ToArray()) }
  catch { $resp = $_.Exception.Response; $s = [System.IO.StreamReader]::new($resp.GetResponseStream(), [System.Text.Encoding]::UTF8).ReadToEnd(); "$metodo $ruta -> $([int]$resp.StatusCode): $s" }
}
Call GET '/planes'
Call GET '/socios'
'--- check-in'
Call POST '/check-in' @{ documento = '1001001001' }
Call POST '/check-in' @{ documento = '1002002002' }
Call POST '/check-in' @{ documento = '1003003003' }
Call POST '/check-in' @{ documento = '999' }
'--- asignar'
Call POST '/membresias' @{ socio_id = 1; plan_id = 2; tipo_tarifa = 'MENSUAL' }
Call POST '/membresias' @{ socio_id = 3; plan_id = 2; tipo_tarifa = 'ANUAL' }
'--- congelar'
Call POST '/membresias/2/congelar' @{ dias = 3 }
Call POST '/membresias/2/congelar' @{ dias = 45 }
Call POST '/membresias/2/congelar' @{ dias = 10 }
Call POST '/check-in' @{ documento = '1002002002' }
Call POST '/membresias/2/reactivar'
Call POST '/membresias/2/reactivar'
Call POST '/membresias/1/congelar' @{ dias = 7 }
