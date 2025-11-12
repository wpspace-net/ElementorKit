import React from 'react'
import PhotoBanner from './PhotoBanner'
import GlobalConfigProvider from '../Contexts/GlobalConfigProvider'

export default { title: 'banners' }

export const photos = () => {
  return (
    <GlobalConfigProvider config={{
      api_url: 'https://api.elementorkit.site'
    }}
    >
      <PhotoBanner />
    </GlobalConfigProvider>
  )
}
